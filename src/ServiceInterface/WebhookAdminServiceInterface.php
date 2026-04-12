<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

/**
 * Defines the contract for webhook admin service.
 */
interface WebhookAdminServiceInterface
{
    /** @return array{kid:string,secret:string} */
    public function registerKey(string $name): array;

    /** @param array<string,mixed> $payload */
    public function scheduleDelivery(string $target, array $payload): int;

    /**
     * Handles the requeue workflow.
     */
    public function requeue(int $limit): int;
}
