<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\InfrastructureInterface;

use App\ValueObject\WebhookPayloadRequest;

/**
 * Defines the contract for webhook sender.
 */
interface WebhookSenderInterface
{
    public function send(WebhookPayloadRequest $request): void;
}
