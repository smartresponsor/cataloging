<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\ServiceInterface;

interface WebhookClientInterface
{
    public function send(string $endpoint, string $event, array $payload): bool;
}
