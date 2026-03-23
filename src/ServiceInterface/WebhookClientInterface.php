<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

interface WebhookClientInterface
{
    /** @param array<string,mixed> $payload */
    public function send(string $endpoint, string $event, array $payload): bool;
}
