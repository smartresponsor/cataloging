<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

interface CategorySyndicationDestinationPolicyInterface
{
    public function assertDestinationType(string $destinationType): void;

    public function assertDeliveryMode(string $deliveryMode): void;

    /**
     * @param array<string,mixed> $settings
     *
     * @return array<string,string>
     */
    public function normalizeSettings(array $settings): array;
}
