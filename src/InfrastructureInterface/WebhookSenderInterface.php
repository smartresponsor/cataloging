<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\InfrastructureInterface;

interface WebhookSenderInterface
{
    /** @param array<string, mixed> $payload */
    public function send(array $payload): void;
}
