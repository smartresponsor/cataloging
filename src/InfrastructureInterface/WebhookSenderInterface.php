<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\InfrastructureInterface;

use App\Cataloging\ValueObject\WebhookPayloadRequest;

/**
 * Defines the contract for webhook sender.
 */
interface WebhookSenderInterface
{
    public function send(WebhookPayloadRequest $request): void;
}
