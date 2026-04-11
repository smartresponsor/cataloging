<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\ValueObject\WebhookDispatchRequest;

/**
 * Defines the contract for webhook client.
 */
interface WebhookClientInterface
{
    public function send(WebhookDispatchRequest $request): bool;
}
