<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Infrastructure;

use App\InfrastructureInterface\WebhookSenderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HttpWebhookSender implements WebhookSenderInterface
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
