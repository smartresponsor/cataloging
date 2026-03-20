<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Security;

use Psr\Log\LoggerInterface;

final class RateLimitResolver
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function onLimitExceeded(string $route, string $user): void
    {
        $this->logger->warning('category.ratelimit', ['route' => $route, 'user' => $user]);
    }
}
