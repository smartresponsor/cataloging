<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Infra\Category;

use Psr\Log\LoggerInterface;

final class CategoryAuditLogger
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function log(string $action, array $context = []): void
    {
        $this->logger->info('category.audit', ['action' => $action] + $context);
    }
}
