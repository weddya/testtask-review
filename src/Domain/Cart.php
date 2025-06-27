<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Domain;

final readonly class Cart
{
    /**
     * @param CartItem[] $items
     */
    public function __construct(
        private string $uuid,
        private Customer $customer,
        private string $paymentMethod = '',
        private array $items = [],
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    /**
     * @return CartItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(CartItem $item): self
    {
        $newItems = $this->items;
        $newItems[] = $item;
        
        return new self(
            uuid: $this->uuid,
            customer: $this->customer,
            paymentMethod: $this->paymentMethod,
            items: $newItems,
        );
    }

    public function getTotal(): float
    {
        return array_reduce(
            $this->items,
            fn(float $total, CartItem $item) => $total + $item->getTotal(),
            0.0
        );
    }

    public function getItemCount(): int
    {
        return count($this->items);
    }
}
