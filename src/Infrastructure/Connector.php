<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

use Raketa\BackendTestTask\Exception\ConnectorException;
use Redis;
use RedisException;

final readonly class Connector
{
    public function __construct(
        private Redis $redis
    ) {
    }

    /**
     * @throws ConnectorException
     */
    public function get(string $key): mixed
    {
        try {
            $data = $this->redis->get($key);
            if ($data === false) {
                return null;
            }

            $unserialized = unserialize($data);
            if ($unserialized === false) {
                throw new ConnectorException('Failed to unserialize data');
            }

            return $unserialized;
        } catch (RedisException $e) {
            throw new ConnectorException('Redis connection error', $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new ConnectorException('Failed to retrieve data', 0, $e);
        }
    }

    /**
     * @throws ConnectorException
     */
    public function set(string $key, mixed $value, int $ttl): void
    {
        try {
            $serialized = serialize($value);
            $this->redis->setex($key, $ttl, $serialized);
        } catch (RedisException $e) {
            throw new ConnectorException('Redis connection error', $e->getCode(), $e);
        }
    }

    public function has(string $key): bool
    {
        try {
            return (bool) $this->redis->exists($key);
        } catch (RedisException) {
            return false;
        }
    }
}