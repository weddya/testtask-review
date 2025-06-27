<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure\Configuration;

final readonly class RedisConfig
{
    public function __construct(
        public string $host,
        public int $port = 6379,
        public ?string $password = null,
        public int $database = 1,
        public int $timeout = 0
    ) {
    }
}