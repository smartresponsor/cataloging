<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the webhook config policy application service.
 */
final class WebhookConfigPolicy
{
    /**
     * Validates the current input against the component rules.
     */
    public function validate(WebhookEndpoint $e): void
    {
        if (!preg_match('~^https?://~', $e->url())) {
            throw new \InvalidArgumentException('Endpoint URL must be http/https');
        }
        if (strlen($e->secret()) < 16) {
            throw new \InvalidArgumentException('Secret too short');
        }
    }

    /**
     * Handles the rotate secret workflow.
     */
    public function rotateSecret(WebhookEndpoint $e, string $new): WebhookEndpoint
    {
        return new WebhookEndpoint($e->id(), $e->url(), $new, $e->active());
    }
}
