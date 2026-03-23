<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Security;

use Psr\Log\LoggerInterface;

final class ConsentChecker
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function ensure(array $context): void
    {
        if (!($context['consent'] ?? false)) {
            $this->logger->warning('category.consent.missing', $context);
            throw new \RuntimeException('Consent is required');
        }
    }
}
