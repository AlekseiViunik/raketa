<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Psr\Log\LoggerInterface;

readonly class CartView
{
    public function __construct(
        private ProductRepository $productRepository,
        private LoggerInterface $logger
    ) {
    }

    public function toArray(Cart $cart): array
    {
        $data = [
            'uuid' => $cart->getUuid(),
            'customer' => [
                'id' => $cart->getCustomer()->getId(),
                'name' => implode(' ', [
                    $cart->getCustomer()->getLastName(),
                    $cart->getCustomer()->getFirstName(),
                    $cart->getCustomer()->getMiddleName(),
                ]),
                'email' => $cart->getCustomer()->getEmail(),
            ],
            'payment_method' => $cart->getPaymentMethod(),
        ];

        $total = 0;
        $data['items'] = [];

        try {
            $productUuids = array_map(fn($item) => $item->getProductUuid(), $cart->getItems());
            $products = $this->productRepository->getByUuids($productUuids);

            if (empty($products)) {
                $this->logger->warning("No products found for the provided UUIDs: " . implode(', ', $productUuids));
            }

            $productsMap = [];
            foreach ($products as $product) {
                $productsMap[$product->getUuid()] = $product;
            }

            foreach ($cart->getItems() as $item) {
                $product = $productsMap[$item->getProductUuid()] ?? null;

                if (!$product) {
                    $this->logger->warning("Product not found for UUID: " . $item->getProductUuid());
                    continue;  // Пропускаем товар, если его нет в списке
                }

                $itemTotal = $item->getPrice() * $item->getQuantity();
                $total += $itemTotal;

                $data['items'][] = [
                    'uuid' => $item->getUuid(),
                    'price' => $item->getPrice(),
                    'total' => $itemTotal,
                    'quantity' => $item->getQuantity(),
                    'product' => [
                        'id' => $product->getId(),
                        'uuid' => $product->getUuid(),
                        'name' => $product->getName(),
                        'thumbnail' => $product->getThumbnail(),
                        'price' => $product->getPrice(),
                    ],
                ];
            }

            $data['total'] = $total;

        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch products for cart: ' . $cart->getUuid(), ['exception' => $e]);
        }

        return $data;
    }
}
