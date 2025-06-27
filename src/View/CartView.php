<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain;
use Raketa\BackendTestTask\Repository\Entity\Product;

final readonly class CartView
{
    public function __construct() {}

    public function toArray(Cart $cart, array $products): array
    {
        return [
            'uuid' => $cart->getUuid(),
            'customer' => $this->formatCustomer($cart->getCustomer()),
            'payment_method' => $cart->getPaymentMethod(),
            'items' => $this->formatItems($cart->getItems(), $products),
            'total' => $cart->getTotal(),
            'item_count' => $cart->getItemCount(),
        ];
    }

    private function formatCustomer(Domain\Customer $customer): array
    {
        return [
            'id' => $customer->getId(),
            'name' => $customer->getFullName(),
            'email' => $customer->getEmail(),
        ];
    }

    /**
     * @param Domain\CartItem[] $items
     * @param array<string, Product> $products
     */
    private function formatItems(array $items, array $products): array
    {
        if (empty($items)) {
            return [];
        }

        return array_map(
            fn(Domain\CartItem $item) => $this->formatItem($item, $products),
            $items
        );
    }

    /**
     * @param array<string, Product> $products
     */
    private function formatItem(Domain\CartItem $item, array $products): array
    {
        $product = $products[$item->getProductUuid()] ?? null;

        $productData = $product ? [
            'id' => $product->getId(),
            'uuid' => $product->getUuid(),
            'name' => $product->getName(),
            'thumbnail' => $product->getThumbnail(),
            'price' => $product->getPrice(),
        ] : [
            'id' => 0,
            'uuid' => $item->getProductUuid(),
            'name' => 'Product not available',
            'thumbnail' => '',
            'price' => 0.0,
        ];

        return [
            'uuid' => $item->getUuid(),
            'price' => $item->getPrice(),
            'quantity' => $item->getQuantity(),
            'total' => $item->getTotal(),
            'product' => $productData,
        ];
    }
}