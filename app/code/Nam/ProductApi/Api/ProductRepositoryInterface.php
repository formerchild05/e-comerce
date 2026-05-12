<?php
namespace Nam\ProductApi\Api;

use Nam\ProductApi\Api\Data\ProductInterface;

interface ProductRepositoryInterface
{
    /**
     * Get all products
     *
     * @return ProductInterface[]
     */
    public function getProducts();

    /**
     * Get products by category ID
     *
     * @param int $categoryId
     * @return ProductInterface[]
     */
    public function getProductsByCategory($categoryId);
}
