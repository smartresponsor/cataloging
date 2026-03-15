<?php

declare(strict_types=1);

namespace App\ServiceInterface\Integration;

interface WebhookClientInterface
{
    public function send(string $endpoint, string $event, array $payload): bool;
}
