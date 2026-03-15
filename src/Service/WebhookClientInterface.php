<?php

declare(strict_types=1);

namespace App\Service;

interface WebhookClientInterface
{
    public function send(string $endpoint, string $event, array $payload): bool;
}
