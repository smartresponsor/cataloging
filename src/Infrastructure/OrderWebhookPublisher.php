<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Infrastructure;

use Symfony\Contracts\HttpClient\HttpClientInterface;
/**
 * Provides the order webhook publisher implementation.
 */
final class OrderWebhookPublisher
{
    /**
     * Initializes the order webhook publisher service collaborators.
     */
    public function __construct(private readonly HttpClientInterface $client, private readonly string $url)
    {
    }

    /** @param array<string, mixed> $event */
    public function publish(array $event): void
    {
        $this->client->request('POST', $this->url, ['json' => $event, 'timeout' => 5.0]);
    }
}
