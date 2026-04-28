<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the webhook notifier application service.
 */
final readonly class CatalogWebhookNotifierService
{
    /**
     * Initializes the webhook notifier service collaborators.
     */
    public function __construct(private string $endpoint)
    {
    }

    /** @param array<string,mixed> $payload */
    public function notify(string $event, array $payload): void
    {
        if ('' === trim($this->endpoint)) {
            return;
        }

        unset($event, $payload);
    }
}
