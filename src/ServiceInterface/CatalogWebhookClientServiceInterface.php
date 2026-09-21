<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\ValueObject\WebhookDispatchMessageRequest;

/**
 * Defines the contract for webhook client.
 */
interface CatalogWebhookClientServiceInterface
{
    public function send(WebhookDispatchMessageRequest $request): bool;
}
