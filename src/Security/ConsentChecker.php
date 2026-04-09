<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Security;

use Psr\Log\LoggerInterface;

/**
 * Provides the consent checker implementation.
 */
final readonly class ConsentChecker
{
    /**
     * Initializes the consent checker service collaborators.
     */
    public function __construct(private LoggerInterface $logger)
    {
    }

    /** @param array<string,mixed> $context */
    public function ensure(array $context): void
    {
        if (!($context['consent'] ?? false)) {
            $this->logger->warning('category.consent.missing', $context);
            throw new \RuntimeException('Consent is required');
        }
    }
}
