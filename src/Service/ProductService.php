<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Service;

use Doctrine\DBAL\Exception;
use Raketa\BackendTestTask\Repository\Entity\Product;
use Raketa\BackendTestTask\Repository\ProductRepository;

final readonly class ProductService
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {
    }

    /**
     * @param string $categoryId
     * @return Product[]
     * @throws Exception
     */
    public function getByCategory(string $categoryId): array
    {
        return $this->productRepository->getByCategory($categoryId);
    }
}