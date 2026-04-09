<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Infrastructure;

use Psr\Log\LoggerInterface;

/**
 * Provides the category audit logger implementation.
 */
final readonly class CategoryAuditLogger
{
    /**
     * Initializes the category audit logger service collaborators.
     */
    public function __construct(private LoggerInterface $logger)
    {
    }

    /** @param array<string, mixed> $context */
    public function log(string $action, array $context = []): void
    {
        $this->logger->info('category.audit', ['action' => $action] + $context);
    }
}
