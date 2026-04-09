<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Observability\RequestCorrelationIdProvider;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Provides the webhook dispatcher application service.
 */
final readonly class WebhookDispatcher
{
    /**
     * Initializes the webhook dispatcher service collaborators.
     */
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $secret = 'changeme',
        private ?RequestCorrelationIdProvider $correlationIdProvider = null,
    ) {
    }

    /**
     * @param string              $event
     * @param array<string,mixed> $payload
     * @param string              $endpoint
     *
     * @throws TransportExceptionInterface
     * @throws \JsonException
     */
    public function dispatch(string $event, array $payload, string $endpoint): void
    {
        $body = json_encode(['event' => $event, 'payload' => $payload], JSON_THROW_ON_ERROR);
        $sig = hash_hmac('sha256', $body, $this->secret);
        $headers = [
            'X-Category-Event' => $event,
            'X-Category-Signature' => $sig,
            'Content-Type' => 'application/json',
        ];

        $correlationId = $this->correlationIdProvider?->current();
        if (is_string($correlationId) && '' !== $correlationId) {
            $headers[RequestCorrelationIdProvider::HEADER] = $correlationId;
        }

        $this->httpClient->request('POST', $endpoint, [
            'headers' => $headers,
            'timeout' => 5.0,
            'body' => $body,
        ]);
    }
}
