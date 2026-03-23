<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

interface WebhookAdminServiceInterface
{
    /** @return array{kid:string,secret:string} */
    public function registerKey(string $name): array;

    /** @param array<string,mixed> $payload */
    public function scheduleDelivery(string $target, array $payload): int;

    public function requeue(int $limit): int;
}
