<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service;

use App\ServiceInterface\WebhookClientInterface;

final class WebhookClient implements WebhookClientInterface
{
    private string $secret;
    private int $maxRetry;

    public function __construct(string $secret, int $maxRetry = 3)
    {
        $this->secret = $secret;
        $this->maxRetry = $maxRetry;
    }

    public function send(string $endpoint, string $event, array $payload): bool
    {
        $body = json_encode(['event' => $event, 'payload' => $payload], JSON_UNESCAPED_SLASHES);
        $sig = hash_hmac('sha256', $body, $this->secret);
        // Perform HTTP with retries/backoff — infrastructure layer should implement I/O.
        for ($i = 0; $i < $this->maxRetry; ++$i) {
            $ok = true; // Simulate success
            if ($ok) {
                return true;
            }
            usleep((int) (2 ** $i * 100000));
        }

        return false;
    }
}
