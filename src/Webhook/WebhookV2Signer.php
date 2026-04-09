<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Webhook;
/**
 * Provides the webhook v2 signer implementation.
 */
final class WebhookV2Signer
{
    /**
     * Initializes the webhook v2 signer service collaborators.
     */
    public function __construct(private readonly string $secret = 'changeme')
    {
    }

    /** @return array{timestamp:int,signature:string} */
    public function sign(string $payload): array
    {
        $ts = time();
        $sig = hash_hmac('sha256', $ts.'.'.$payload, $this->secret);

        return [
            'timestamp' => $ts,
            'signature' => $sig,
        ];
    }
    /**
     * Handles the verify workflow.
     */
    public function verify(string $payload, int $ts, string $given): bool
    {
        $expected = hash_hmac('sha256', $ts.'.'.$payload, $this->secret);

        return hash_equals($expected, $given);
    }
}
