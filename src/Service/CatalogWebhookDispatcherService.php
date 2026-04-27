<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Observability\RequestCorrelationIdProvider;
use App\Cataloging\ValueObject\WebhookDispatchRequest;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Provides the webhook dispatcher application service.
 */
final readonly class CatalogWebhookDispatcherService
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
     * @throws TransportExceptionInterface
     * @throws \JsonException
     */
    public function dispatch(WebhookDispatchRequest $request): void
    {
        $body = json_encode(['event' => $request->event, 'payload' => $request->payload], JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $body, $this->secret);
        $headers = [
            'X-Category-Event' => $request->event,
            'X-Category-Signature' => $signature,
            'Content-Type' => 'application/json',
        ];

        $correlationId = $this->correlationIdProvider?->current();
        if (is_string($correlationId) && '' !== $correlationId) {
            $headers[RequestCorrelationIdProvider::HEADER] = $correlationId;
        }

        $this->httpClient->request('POST', $request->endpoint, [
            'headers' => $headers,
            'timeout' => 5.0,
            'body' => $body,
        ]);
    }
}
