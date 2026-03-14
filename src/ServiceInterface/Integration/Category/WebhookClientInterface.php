<?php

declare(strict_types=1);

namespace App\ServiceInterface\Integration\Category;

interface WebhookClientInterface
{
    public function send(string $endpoint, string $event, array $payload): bool;
}
