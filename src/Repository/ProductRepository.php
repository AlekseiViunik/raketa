<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Repository;

use Doctrine\DBAL\Connection;
use Exception;
use Raketa\BackendTestTask\Repository\Entity\Product;

class ProductRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getByUuid(string $uuid): Product
    {
        $sql = "SELECT * FROM products WHERE uuid = :uuid";
        $row = $this->connection->fetchAssociative($sql, ['uuid' => $uuid]);

        if (empty($row)) {
            throw new Exception('Product not found');
        }

        return $this->make($row);
    }

    public function getByCategory(string $category): array
    {
        $sql = "SELECT * FROM products WHERE is_active = 1 AND category = :category";
        $rows = $this->connection->fetchAllAssociative($sql, ['category' => $category]);

        if (empty($rows)) {
            return [];
        }

        return array_map(
            fn (array $row): Product => $this->make($row),
            $rows
        );
    }

    public function make(array $row): Product
    {
        return new Product(
            $row['id'],
            $row['uuid'],
            (bool) $row['is_active'],
            $row['category'],
            $row['name'],
            $row['description'] ?? '', // nullable
            $row['thumbnail'] ?? '',
            $row['price'],
        );
    }
}
