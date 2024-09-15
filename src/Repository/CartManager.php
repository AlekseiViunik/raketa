<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Repository;

use Exception;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Infrastructure\ConnectorFacade;

class CartManager extends ConnectorFacade
{
    private LoggerInterface $logger;

    public function __construct(string $host, int $port, ?string $password, LoggerInterface $logger)
    {
        parent::__construct($host, $port, $password, 1);  // Выбор базы данных 1
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function saveCart(Cart $cart): void
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $this->connector->set(session_id(), $cart);
        } catch (\Exception $e) {
            $this->logger->error('Error saving cart: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * @return ?Cart
     */
    public function getCart(): ?Cart
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $cart = $this->connector->get(session_id());
            return $cart ?: new Cart(session_id(), []);  // Если корзина не найдена, создаем новую
        } catch (\Exception $e) {
            $this->logger->error('Error retrieving cart: ' . $e->getMessage(), ['exception' => $e]);
            return null;
        }
    }
}
