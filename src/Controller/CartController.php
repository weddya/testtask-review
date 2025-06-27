<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Core\Http\JsonResponse;
use Raketa\BackendTestTask\Exception;
use Raketa\BackendTestTask\Service\CartService;
use Raketa\BackendTestTask\View\CartView;

final readonly class CartController extends AbstractController
{
    public function __construct(
        private CartService $cartService,
        private CartView $cartView,
        private LoggerInterface $logger,
    ) {
    }

    public function add(RequestInterface $request): JsonResponse
    {
        try {
            $body = $this->parseRequestBody($request);

            $sessionId = session_id();
            $this->cartService->addItemToCart(
                $sessionId,
                $body['productUuid'],
                $body['quantity'],
            );

            $data = $this->cartService->getCartForView($sessionId);
            return $this->createSuccessResponse(['cart' => $this->cartView->toArray($data['cart'], $data['products'])]);
        } catch (Exception\DomainException $e) {
            $this->logger->error('Domain error in CartController', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            return $this->createErrorResponse($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            $this->logger->error('Unexpected error in CartController', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->createErrorResponse('Internal server error', 500);
        }
    }

    public function get(): JsonResponse
    {
        try {
            $sessionId = session_id();
            $data = $this->cartService->getCartForView($sessionId);

            return $this->createSuccessResponse(['cart' => $this->cartView->toArray($data['cart'], $data['products'])]);
        } catch (Exception\CartNotFoundException $e) {
            return $this->createErrorResponse($e->getMessage(), 404);
        } catch (\Exception $e) {
            $this->logger->error('Unexpected error in GetCartController', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->createErrorResponse('Internal server error', 500);
        }
    }
}