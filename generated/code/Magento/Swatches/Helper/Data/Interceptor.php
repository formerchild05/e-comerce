<?php
namespace Magento\Swatches\Helper\Data;

/**
 * Interceptor class for @see \Magento\Swatches\Helper\Data
 */
class Interceptor extends \Magento\Swatches\Helper\Data implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory $swatchCollectionFactory, \Magento\Catalog\Model\Product\Image\UrlBuilder $urlBuilder, ?\Magento\Framework\Serialize\Serializer\Json $serializer = null, ?\Magento\Swatches\Model\SwatchAttributesProvider $swatchAttributesProvider = null, ?\Magento\Swatches\Model\SwatchAttributeType $swatchTypeChecker = null)
    {
        $this->___init();
        parent::__construct($productCollectionFactory, $productRepository, $storeManager, $swatchCollectionFactory, $urlBuilder, $serializer, $swatchAttributesProvider, $swatchTypeChecker);
    }

    /**
     * {@inheritdoc}
     */
    public function assembleAdditionalDataEavAttribute(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'assembleAdditionalDataEavAttribute');
        return $pluginInfo ? $this->___callPlugins('assembleAdditionalDataEavAttribute', func_get_args(), $pluginInfo) : parent::assembleAdditionalDataEavAttribute($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function loadFirstVariationWithSwatchImage(\Magento\Catalog\Api\Data\ProductInterface $configurableProduct, array $requiredAttributes)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadFirstVariationWithSwatchImage');
        return $pluginInfo ? $this->___callPlugins('loadFirstVariationWithSwatchImage', func_get_args(), $pluginInfo) : parent::loadFirstVariationWithSwatchImage($configurableProduct, $requiredAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function loadFirstVariationWithImage(\Magento\Catalog\Api\Data\ProductInterface $configurableProduct, array $requiredAttributes)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadFirstVariationWithImage');
        return $pluginInfo ? $this->___callPlugins('loadFirstVariationWithImage', func_get_args(), $pluginInfo) : parent::loadFirstVariationWithImage($configurableProduct, $requiredAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function loadVariationByFallback(\Magento\Catalog\Api\Data\ProductInterface $parentProduct, array $attributes)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadVariationByFallback');
        return $pluginInfo ? $this->___callPlugins('loadVariationByFallback', func_get_args(), $pluginInfo) : parent::loadVariationByFallback($parentProduct, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductMediaGallery(\Magento\Catalog\Model\Product $product): array
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductMediaGallery');
        return $pluginInfo ? $this->___callPlugins('getProductMediaGallery', func_get_args(), $pluginInfo) : parent::getProductMediaGallery($product);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributesFromConfigurable(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributesFromConfigurable');
        return $pluginInfo ? $this->___callPlugins('getAttributesFromConfigurable', func_get_args(), $pluginInfo) : parent::getAttributesFromConfigurable($product);
    }

    /**
     * {@inheritdoc}
     */
    public function getSwatchAttributesAsArray(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSwatchAttributesAsArray');
        return $pluginInfo ? $this->___callPlugins('getSwatchAttributesAsArray', func_get_args(), $pluginInfo) : parent::getSwatchAttributesAsArray($product);
    }

    /**
     * {@inheritdoc}
     */
    public function getSwatchesByOptionsId(array $optionIds)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSwatchesByOptionsId');
        return $pluginInfo ? $this->___callPlugins('getSwatchesByOptionsId', func_get_args(), $pluginInfo) : parent::getSwatchesByOptionsId($optionIds);
    }

    /**
     * {@inheritdoc}
     */
    public function isProductHasSwatch(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isProductHasSwatch');
        return $pluginInfo ? $this->___callPlugins('isProductHasSwatch', func_get_args(), $pluginInfo) : parent::isProductHasSwatch($product);
    }

    /**
     * {@inheritdoc}
     */
    public function isSwatchAttribute(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isSwatchAttribute');
        return $pluginInfo ? $this->___callPlugins('isSwatchAttribute', func_get_args(), $pluginInfo) : parent::isSwatchAttribute($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function isVisualSwatch(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isVisualSwatch');
        return $pluginInfo ? $this->___callPlugins('isVisualSwatch', func_get_args(), $pluginInfo) : parent::isVisualSwatch($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function isTextSwatch(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isTextSwatch');
        return $pluginInfo ? $this->___callPlugins('isTextSwatch', func_get_args(), $pluginInfo) : parent::isTextSwatch($attribute);
    }
}
