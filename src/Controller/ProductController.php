<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Core\Http\JsonResponse;
use Raketa\BackendTestTask\Service\ProductService;
use Raketa\BackendTestTask\View\ProductsView;

final readonly class ProductController extends AbstractController
{
    public function __construct(
        private ProductService $productService,
        private ProductsView $productsView,
        private LoggerInterface $logger,
    ) {
    }

    public function getByCategory(RequestInterface $request): JsonResponse
    {
        try {
            $body = $this->parseRequestBody($request);
            $products = $this->productService->getByCategory($body['category']);

            return $this->createSuccessResponse(['products' => $this->productsView->productList($products)]);
        } catch (\Exception $e) {
            $this->logger->error('Unexpected error in ProductController', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->createErrorResponse('Internal server error', 500);
        }
    }
}