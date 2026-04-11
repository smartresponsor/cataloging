<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Infrastructure;

use App\InfrastructureInterface\WebhookSenderInterface;
use App\ValueObject\WebhookPayloadRequest;
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
     * @throws TransportExceptionInterface
     */
    public function send(WebhookPayloadRequest $request): void
    {
        $this->client->request('POST', $this->endpoint, ['json' => $request->payload, 'timeout' => 5.0]);
    }
}
