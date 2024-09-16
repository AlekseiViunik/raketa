<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Repository;

use Exception;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\Customer;
use Raketa\BackendTestTask\Infrastructure\ConnectorFacade;

class CartManager extends ConnectorFacade
{

    public function __construct(string $host, int $port, ?string $password, LoggerInterface $logger)
    {
        parent::__construct($host, $port, $password, 1, $logger);
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

            //TODO Реализовать получение дынных пользователя из сессии
            $customer = new Customer(1, 'First', 'Last', 'Middle', 'email@example.com'); //Заглушка
            return $cart ?: new Cart(session_id(), $customer, 'default_payment_method', []);  // Если корзина не найдена, создаем новую
        } catch (\Exception $e) {
            $this->logger->error('Error retrieving cart: ' . $e->getMessage(), ['exception' => $e]);
            return null;
        }
    }
}
