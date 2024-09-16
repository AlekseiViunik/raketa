<?php

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Repository\Entity\Product;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Psr\Log\LoggerInterface;

readonly class ProductsView
{
    public function __construct(
        private ProductRepository $productRepository,
        private LoggerInterface $logger
    ) {
    }

    public function toArray(string $category): array
    {
        try {
            $products = $this->productRepository->getByCategory($category);
        } catch (\Exception $e) {
            $this->logger->error('Failed to retrieve products by category', [
                'error' => $e->getMessage(),
                'category' => $category
            ]);
            return [];
        }
        return array_map(
            fn (Product $product) => [
                'id' => $product->getId(),
                'uuid' => $product->getUuid(),
                'category' => $product->getCategory(),
                'description' => $product->getDescription() ?? '',
                'thumbnail' => $product->getThumbnail() ?? '',
                'price' => $product->getPrice(),
            ],
            $products
        );
    }
}
