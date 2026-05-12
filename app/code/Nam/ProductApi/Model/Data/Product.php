<?php
namespace Nam\ProductApi\Model\Data;

use Nam\ProductApi\Api\Data\ProductInterface;

class Product implements ProductInterface
{
    private $productId;
    private $sku;
    private $name;
    private $price;
    private $status;
    private $category;
    private $description;
    private $image;

    public function __construct(
        $productId,
        $sku,
        $name,
        $price,
        $status,
        $category,
        $description,
        $image
    ) {
        $this->productId = $productId;
        $this->sku = $sku;
        $this->name = $name;
        $this->price = $price;
        $this->status = $status;
        $this->category = $category;
        $this->description = $description;
        $this->image = $image;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getImage()
    {
        return $this->image;
    }
}
