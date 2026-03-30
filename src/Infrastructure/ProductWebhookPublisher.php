<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Infrastructure;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ProductWebhookPublisher
{
    public function __construct(private readonly HttpClientInterface $client, private readonly string $url)
    {
    }

    /** @param array<string, mixed> $event */
    public function publish(array $event): void
    {
        $this->client->request('POST', $this->url, ['json' => $event]);
    }
}
