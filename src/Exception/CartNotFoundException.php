<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Exception;

class CartNotFoundException extends DomainException
{
    public function __construct(string $sessionId)
    {
        parent::__construct(sprintf('Cart for session "%s" not found', $sessionId), 404);
    }
}