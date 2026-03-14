<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Integration\Category;

use App\Service\Security\Category\OidcJwtVerifier;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class WebhookClient
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly OidcJwtVerifier $verifier,
        private readonly string $endpoint,
    ) {
    }

    public function dispatch(array $payload): void
    {
        $jwt = $this->verifier->sign($payload);
        $this->client->request('POST', $this->endpoint, [
            'json' => $payload,
            'headers' => [
                'Authorization' => 'Bearer '.$jwt,
                'X-SR-Source' => 'category',
            ],
        ]);
    }
}
