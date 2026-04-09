<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Infrastructure;

use App\InfrastructureInterface\WebhookSenderInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Provides the http webhook sender implementation.
 */
final readonly class HttpWebhookSender implements WebhookSenderInterface
{
    /**
     * Initializes the http webhook sender service collaborators.
     */
    public function __construct(
        private HttpClientInterface $client,
        private string $endpoint,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @throws TransportExceptionInterface
     */
    public function send(array $payload): void
    {
        $this->client->request('POST', $this->endpoint, ['json' => $payload, 'timeout' => 5.0]);
    }
}
