<?php

declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Infrastructure;

use App\InfrastructureInterface\CategoryWebhookSenderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CategoryHttpWebhookSender implements CategoryWebhookSenderInterface
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $endpoint,
    ) {
    }

    public function send(array $payload): void
    {
        $this->client->request('POST', $this->endpoint, ['json' => $payload]);
    }
}
