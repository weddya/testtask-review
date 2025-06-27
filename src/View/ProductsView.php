<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Repository\Entity\Product;

final readonly class ProductsView
{
    public function __construct() {}

    public function productList(array $products): array
    {
        return array_map(
            fn(Product $product) => $this->toArray($product),
            $products
        );
    }

    public function toArray(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'uuid' => $product->getUuid(),
            'name' => $product->getName(),
            'category' => $product->getCategory(),
            'description' => $product->getDescription(),
            'thumbnail' => $product->getThumbnail(),
            'price' => $product->getPrice(),
        ];
    }
}