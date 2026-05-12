<?php
namespace Nam\ProductApi\Model;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Nam\ProductApi\Api\ProductRepositoryInterface;
use Nam\ProductApi\Api\Data\ProductInterface;
use Nam\ProductApi\Model\Data\Product as ProductData;

class ProductRepository implements ProductRepositoryInterface
{
    private $objectManager;
    private $categoryRepository;

    public function __construct(
        ObjectManagerInterface $objectManager,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->objectManager = $objectManager;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all products
     *
     * @return ProductInterface[]
     */
    public function getProducts()
    {
        $collection = $this->createProductCollection();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('status', ['eq' => ProductStatus::STATUS_ENABLED]);

        $products = [];
        foreach ($collection as $product) {
            $products[] = $this->convertProductToData($product);
        }

        return $products;
    }

    /**
     * Get products by category ID
     *
     * @param int $categoryId
     * @return ProductInterface[]
     */
    public function getProductsByCategory($categoryId)
    {
        $collection = $this->createProductCollection();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('status', ['eq' => ProductStatus::STATUS_ENABLED]);
        $collection->addCategoryFilter($this->categoryRepository->get((int) $categoryId));

        $products = [];
        foreach ($collection as $product) {
            $products[] = $this->convertProductToData($product);
        }

        return $products;
    }

    /**
     * Convert product model to data interface
     *
     * @param Product $product
     * @return ProductInterface
     */
    private function convertProductToData(Product $product)
    {
        $categoryNames = $this->getCategoryNames($product->getCategoryIds());

        return new ProductData(
            $product->getId(),
            $product->getSku(),
            $product->getName(),
            $product->getPrice(),
            $product->getStatus(),
            $categoryNames,
            $product->getShortDescription() ?: '',
            $product->getImage() ?: ''
        );
    }

    /**
     * Resolve category names from category IDs.
     *
     * @param array $categoryIds
     * @return string[]
     */
    private function getCategoryNames(array $categoryIds)
    {
        $categoryIds = array_filter(array_map('intval', $categoryIds));

        if (!$categoryIds) {
            return [];
        }

        $names = [];
        foreach ($categoryIds as $categoryId) {
            try {
                $name = trim((string) $this->categoryRepository->get($categoryId)->getName());
                if ($name !== '') {
                    $names[] = 'category-' . $categoryId . ': ' . $name;
                }
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                continue;
            }
        }

        return $names;
    }

    /**
     * Create product collection instance.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private function createProductCollection()
    {
        return $this->objectManager->create(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
    }
}
