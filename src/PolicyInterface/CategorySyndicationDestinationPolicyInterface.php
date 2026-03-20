<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\PolicyInterface;

interface CategorySyndicationDestinationPolicyInterface
{
    public function assertDestinationType(string $destinationType): void;

    public function assertDeliveryMode(string $deliveryMode): void;

    /**
     * @param array<string,string> $settings
     */
    public function normalizeSettings(array $settings): array;
}
