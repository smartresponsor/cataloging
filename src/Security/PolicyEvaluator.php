<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Security;

use Psr\Log\LoggerInterface;

final class PolicyEvaluator
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /** @param array<string,mixed> $context */
    public function evaluate(string $action, array $context): bool
    {
        $allowed = ($context['role'] ?? null) === 'ROLE_ADMIN' || 'view' === $action;
        $this->logger->info('category.policy', [
            'action' => $action,
            'allowed' => $allowed,
            'context' => $context,
        ]);

        return $allowed;
    }
}
