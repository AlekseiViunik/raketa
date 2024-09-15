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
    public function saveCart(Cart $cart)
    {
        try {
            $this->connector->set($cart, session_id());
        } catch (Exception $e) {
            $this->logger->error('Error');
        }
    }

    /**
     * @return ?Cart
     */
    public function getCart()
    {
        try {
            return $this->connector->get(session_id());
        } catch (Exception $e) {
            $this->logger->error('Error');
        }

        return new Cart(session_id(), []);
    }
}
