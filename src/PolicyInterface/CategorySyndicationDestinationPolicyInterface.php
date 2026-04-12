<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

/**
 * Defines the contract for category syndication destination policy.
 */
interface CategorySyndicationDestinationPolicyInterface
{
    /**
     * Handles the assert destination type workflow.
     */
    public function assertDestinationType(string $destinationType): void;

    /**
     * Handles the assert delivery mode workflow.
     */
    public function assertDeliveryMode(string $deliveryMode): void;

    /**
     * @param array<string,mixed> $settings
     *
     * @return array<string,string>
     */
    public function normalizeSettings(array $settings): array;
}
