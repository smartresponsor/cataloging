<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class WebhookDispatcher
{
    public function __construct(private readonly HttpClientInterface $httpClient, private readonly string $secret = 'changeme')
    {
    }

    public function dispatch(string $event, array $payload, string $endpoint): void
    {
        $json = ['event' => $event, 'payload' => $payload];
        $body = json_encode($json, JSON_THROW_ON_ERROR);
        $sig = hash_hmac('sha256', $body, $this->secret);

        $response = $this->httpClient->request('POST', $endpoint, [
            'headers' => [
                'X-Category-Event' => $event,
                'X-Category-Signature' => $sig,
                'Content-Type' => 'application/json',
            ],
            'body' => $body,
        ]);

        $response->getStatusCode();
    }
}
