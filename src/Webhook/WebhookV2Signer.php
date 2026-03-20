<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Webhook;

final class WebhookV2Signer
{
    public function __construct(private readonly string $secret = 'changeme')
    {
    }

    public function sign(string $payload): array
    {
        $ts = time();
        $sig = hash_hmac('sha256', $ts.'.'.$payload, $this->secret);

        return [
            'timestamp' => $ts,
            'signature' => $sig,
        ];
    }

    public function verify(string $payload, int $ts, string $given): bool
    {
        $expected = hash_hmac('sha256', $ts.'.'.$payload, $this->secret);

        return hash_equals($expected, $given);
    }
}
