<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Domain;

final readonly class CartItem
{
    public function __construct(
        private string $uuid,
        private string $productUuid,
        private float $price,
        private int $quantity,
    ) {
        if ($price < 0) {
            throw new \InvalidArgumentException('Price cannot be negative');
        }
        
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than 0');
        }
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getProductUuid(): string
    {
        return $this->productUuid;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTotal(): float
    {
        return $this->price * $this->quantity;
    }
}
