<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Security;

use Psr\Log\LoggerInterface;

/**
 * Provides the rate limit resolver implementation.
 */
final readonly class RateLimitResolver
{
    /**
     * Initializes the rate limit resolver service collaborators.
     */
    public function __construct(private LoggerInterface $logger)
    {
    }

    /**
     * Handles the on limit exceeded workflow.
     */
    public function onLimitExceeded(string $route, string $user): void
    {
        $this->logger->warning('category.ratelimit', ['route' => $route, 'user' => $user]);
    }
}
