<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class WebhookDispatcher
{
    public function __construct(private readonly HttpClientInterface $httpClient, private readonly string $secret = 'changeme')
    {
    }

    /** @param array<string,mixed> $payload */
    public function dispatch(string $event, array $payload, string $endpoint): void
    {
        $body = json_encode(['event' => $event, 'payload' => $payload], JSON_THROW_ON_ERROR);
        $sig = hash_hmac('sha256', $body, $this->secret);
        $this->httpClient->request('POST', $endpoint, [
            'headers' => [
                'X-Category-Event' => $event,
                'X-Category-Signature' => $sig,
                'Content-Type' => 'application/json',
            ],
            'body' => $body,
        ]);
    }
}
