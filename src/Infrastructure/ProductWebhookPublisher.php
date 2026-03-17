<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Infrastructure;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ProductWebhookPublisher
{
    public function __construct(private readonly HttpClientInterface $client, private readonly string $url)
    {
    }

    public function publish(array $event): void
    {
        $this->client->request('POST', $this->url, ['json' => $event]);
    }
}
