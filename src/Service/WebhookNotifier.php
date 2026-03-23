<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class WebhookNotifier
{
    private string $endpoint;

    public function __construct(string $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function notify(string $event, array $payload): void
    {
        // Send webhook to $this->endpoint; implementation is infrastructure-specific.
    }
}
