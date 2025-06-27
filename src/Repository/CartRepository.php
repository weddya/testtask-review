<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Repository;

use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Exception\ConnectorException;
use Raketa\BackendTestTask\Infrastructure\ConnectorFacade;

final readonly class CartRepository
{
    private const CART_TTL = 24 * 60 * 60; // 24 hours

    public function __construct(
        private ConnectorFacade $connectorFacade,
        private LoggerInterface $logger
    ) {
    }

    public function saveCart(string $sessionId, Cart $cart): void
    {
        try {
            $this->connectorFacade->getConnector()->set($sessionId, $cart, self::CART_TTL);
        } catch (\Exception $e) {
            $this->logger->error('Failed to save cart', [
                'sessionId' => $sessionId,
                'error' => $e->getMessage()
            ]);
            throw new ConnectorException('Failed to save cart', 0, $e);
        }
    }

    public function getCart(string $sessionId): ?Cart
    {
        try {
            $cart = $this->connectorFacade->getConnector()->get($sessionId);

            if (!$cart instanceof Cart) {
                throw new ConnectorException('Invalid cart data in storage');
            }

            return $cart;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get cart', [
                'sessionId' => $sessionId,
                'error' => $e->getMessage()
            ]);
            throw new ConnectorException('Failed to get cart', 0, $e);
        }
    }
}