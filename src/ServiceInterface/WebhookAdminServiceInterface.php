<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace App\ServiceInterface;

interface WebhookAdminServiceInterface
{
    public function registerKey(string $name): array;

    public function scheduleDelivery(string $target, array $payload): int;

    public function requeue(int $limit): int;
}
