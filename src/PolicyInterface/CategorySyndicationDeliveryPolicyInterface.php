<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

interface CategorySyndicationDeliveryPolicyInterface
{
    public function assertStatus(string $status): void;

    public function assertAttempt(int $attempt): void;

    public function normalizeResponseMessage(string $responseMessage): string;
}
