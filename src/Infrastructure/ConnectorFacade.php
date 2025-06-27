<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

use Raketa\BackendTestTask\Exception\ConnectorException;
use Raketa\BackendTestTask\Infrastructure\Configuration\RedisConfig;
use Redis;
use RedisException;

final class ConnectorFacade
{
    private ?Connector $connector = null;

    public function __construct(
        private readonly RedisConfig $config,
    ) {
    }

    public function getConnector(): Connector
    {
        if ($this->connector === null) {
            $this->connector = $this->buildConnector();
        }

        return $this->connector;
    }

    private function buildConnector(): Connector
    {
        $redis = new Redis();

        try {
            $connected = $redis->connect(
                $this->config->host,
                $this->config->port,
                $this->config->timeout,
            );

            if (!$connected) {
                throw new ConnectorException('Failed to connect to Redis');
            }

            if ($this->config->password !== null) {
                $authResult = $redis->auth($this->config->password);
                if (!$authResult) {
                    throw new ConnectorException('Failed to authenticate with Redis');
                }
            }

            if ($this->config->database !== 0) {
                $selectResult = $redis->select($this->config->database);
                if (!$selectResult) {
                    throw new ConnectorException('Failed to select Redis database');
                }
            }

            return new Connector($redis);
        } catch (RedisException $e) {
            throw new ConnectorException('Redis connection failed', $e->getCode(), $e);
        }
    }
}