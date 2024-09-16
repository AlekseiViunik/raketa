<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Redis;
use RedisException;

class Connector
{
    private Redis $redis;
    private LoggerInterface $logger;

    public function __construct($redis, LoggerInterface $logger)
    {
        $this->redis = $redis;
        $this->logger = $logger;
    }

    /**
     * @throws ConnectorException
     */
    public function get(string $key)
    {
        try {
            return unserialize($this->redis->get($key));
        } catch (RedisException $e) {
            $this->logger->error('Connector error: ' . $e->getMessage(), ['exception' => $e]);
            return [];
        }
    }

    /**
     * @throws ConnectorException
     */
    public function set(string $key, Cart $value)
    {
        try {
            $this->redis->setex($key, 24 * 60 * 60, serialize($value));
        } catch (RedisException $e) {
            $this->logger->error('Connector error: ' . $e->getMessage(), ['exception' => $e]);
            throw new ConnectorException('Connector error', $e->getCode(), $e);
        }
    }

    public function has($key): bool
    {
        return $this->redis->exists($key);
    }
}
