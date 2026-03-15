<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\WebhookAdminServiceInterface;

final class WebhookAdminService implements WebhookAdminServiceInterface
{
    public function registerKey(string $name): array
    {
        // Persist key and return descriptor. Real implementation uses repository.
        return ['name' => $name, 'token' => bin2hex(random_bytes(16))];
    }

    public function scheduleDelivery(string $target, array $payload): int
    {
        // Persist delivery and return ID.
        return random_int(1, 1000000);
    }

    public function requeue(int $limit): int
    {
        // Move DLQ back to delivery queue up to the limit.
        return $limit;
    }
}
