<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Infrastructure;

use App\InfrastructureInterface\WebhookSenderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
/**
 * Provides the http webhook sender implementation.
 */
final class HttpWebhookSender implements WebhookSenderInterface
{
    /**
     * Initializes the http webhook sender service collaborators.
     */
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $endpoint,
    ) {
    }

    /** @param array<string, mixed> $payload */
    public function send(array $payload): void
    {
        $this->client->request('POST', $this->endpoint, ['json' => $payload, 'timeout' => 5.0]);
    }
}
