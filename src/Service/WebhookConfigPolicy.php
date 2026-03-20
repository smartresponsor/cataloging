<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
*/

namespace App\Service;

final class WebhookConfigPolicy
{
    public function validate(WebhookEndpoint $e): void
    {
        if (!preg_match('~^https?://~', $e->url())) {
            throw new \InvalidArgumentException('Endpoint URL must be http/https');
        }
        if (strlen($e->secret()) < 16) {
            throw new \InvalidArgumentException('Secret too short');
        }
    }

    public function rotateSecret(WebhookEndpoint $e, string $new): WebhookEndpoint
    {
        return new WebhookEndpoint($e->id(), $e->url(), $new, $e->active());
    }
}
