<?php
namespace Nam\ProductApi\Api\Data;

interface ProductInterface
{
    const PRODUCT_ID = 'product_id';
    const SKU = 'sku';
    const NAME = 'name';
    const PRICE = 'price';
    const STATUS = 'status';
    const CATEGORY = 'category';
    const DESCRIPTION = 'description';
    const IMAGE = 'image';

    /**
     * Get product ID
     *
     * @return int
     */
    public function getProductId();

    /**
     * Get product SKU
     *
     * @return string
     */
    public function getSku();

    /**
     * Get product name
     *
     * @return string
     */
    public function getName();

    /**
     * Get product price
     *
     * @return float
     */
    public function getPrice();

    /**
     * Get product status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Get product category
     *
        * @return string[]
     */
    public function getCategory();

    /**
     * Get product description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Get product image
     *
     * @return string
     */
    public function getImage();
}
