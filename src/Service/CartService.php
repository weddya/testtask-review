<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Service;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\CartItem;
use Raketa\BackendTestTask\Domain\Customer;
use Raketa\BackendTestTask\Exception;
use Raketa\BackendTestTask\Repository\CartRepository;
use Raketa\BackendTestTask\Repository\Entity\Product;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Ramsey\Uuid\Uuid;

final readonly class CartService
{
    public function __construct(
        private CartRepository    $cartManager,
        private ProductRepository $productRepository,
    ) {
    }

    /**
     * @throws Exception\ProductNotFoundException
     * @throws Exception\ValidationException
     */
    public function addItemToCart(string $sessionId, string $productUuid, int $quantity): Cart
    {
        if ($quantity <= 0) {
            throw new Exception\ValidationException('Quantity must be greater than 0');
        }

        if (!Uuid::isValid($productUuid)) {
            throw new Exception\ValidationException('Invalid product UUID format');
        }

        $product = $this->productRepository->getByUuid($productUuid);

        if (!$product->isActive()) {
            throw new Exception\ProductNotFoundException($productUuid);
        }

        $cart = $this->cartManager->getCart($sessionId);
        if ($cart === null) {
            $cart = new Cart(
                uuid: Uuid::uuid4()->toString(),
                customer: $this->createDefaultCustomer(),
                paymentMethod: '',
                items: [],
            );
        }

        $cartItem = new CartItem(
            uuid: Uuid::uuid4()->toString(),
            productUuid: $product->getUuid(),
            price: $product->getPrice(),
            quantity: $quantity,
        );

        $cart->addItem($cartItem);
        $this->cartManager->saveCart($sessionId, $cart);

        return $cart;
    }

    /**
     * @throws Exception\CartNotFoundException
     */
    public function getCart(string $sessionId): Cart
    {
        $cart = $this->cartManager->getCart($sessionId);
        
        if ($cart === null) {
            throw new Exception\CartNotFoundException($sessionId);
        }

        return $cart;
    }

    /**
     * Get cart with all product data for view layer
     *
     * @return array{cart: Cart, products: array<string, Product>}
     * @throws Exception\CartNotFoundException
     */
    public function getCartForView(string $sessionId): array
    {
        $cart = $this->getCart($sessionId);

        $productUuids = array_map(
            fn(CartItem $item) => $item->getProductUuid(),
            $cart->getItems()
        );

        $products = $this->productRepository->getByUuids($productUuids);

        return [
            'cart' => $cart,
            'products' => $products,
        ];
    }

    private function createDefaultCustomer(): Customer
    {
        return new Customer(
            id: 1,
            firstName: 'John',
            lastName: 'Doe',
            middleName: '',
            email: 'john.doe@example.com',
        );
    }
}