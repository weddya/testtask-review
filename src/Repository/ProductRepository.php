<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Raketa\BackendTestTask\Exception\ProductNotFoundException;
use Raketa\BackendTestTask\Repository\Entity\Product;

final readonly class ProductRepository
{
    public function __construct(
        private Connection $connection
    ) {
    }

    /**
     * @return Product
     * @throws Exception
     * @throws ProductNotFoundException
     */
    public function getByUuid(string $uuid): Product
    {
        $stmt = $this->connection->executeQuery(
            'SELECT * FROM products WHERE uuid = ? AND is_active = 1',
            [
                $uuid,
            ],
        );
        $row = $stmt->fetchAssociative();

        if (!$row) {
            throw new ProductNotFoundException($uuid);
        }

        return $this->hydrateProduct($row);
    }

    /**
     * @return Product[]
     * @throws Exception
     */
    public function getByCategory(string $category): array
    {
        $stmt = $this->connection->executeQuery(
            'SELECT * FROM products WHERE is_active = 1 AND category = ? ORDER BY name',
            [
                $category,
            ],
        );
        $rows = $stmt->fetchAllAssociative();

        return array_map(
            fn(array $row): Product => $this->hydrateProduct($row),
            $rows
        );
    }

    private function hydrateProduct(array $row): Product
    {
        return new Product(
            id: (int) $row['id'],
            uuid: $row['uuid'],
            isActive: (bool) $row['is_active'],
            category: $row['category'],
            name: $row['name'],
            description: $row['description'],
            thumbnail: $row['thumbnail'],
            price: (float) $row['price']
        );
    }

    /**
     * @param string[] $uuids
     * @return array<string, Product>
     * @throws Exception
     */
    public function getByUuids(array $uuids): array
    {
        if (empty($uuids)) {
            return [];
        }

        $placeholders = str_repeat('?,', count($uuids) - 1) . '?';
        $stmt = $this->connection->executeQuery(
            "SELECT * FROM products WHERE uuid IN ({$placeholders}) AND is_active = 1",
            [
                $uuids
            ],
        );
        $rows = $stmt->fetchAllAssociative();

        $products = [];
        foreach ($rows as $row) {
            $product = $this->hydrateProduct($row);
            $products[$product->getUuid()] = $product;
        }

        return $products;
    }
}