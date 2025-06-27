<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Exception;

class ProductNotFoundException extends DomainException
{
    public function __construct(string $uuid)
    {
        parent::__construct(sprintf('Product with UUID "%s" not found', $uuid), 404);
    }
}