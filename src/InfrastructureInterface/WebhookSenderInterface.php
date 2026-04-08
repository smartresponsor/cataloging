<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\InfrastructureInterface;
/**
 * Defines the contract for webhook sender.
 */
interface WebhookSenderInterface
{
    /** @param array<string, mixed> $payload */
    public function send(array $payload): void;
}
