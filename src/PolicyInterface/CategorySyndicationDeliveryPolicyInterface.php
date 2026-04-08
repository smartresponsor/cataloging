<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;
/**
 * Defines the contract for category syndication delivery policy.
 */
interface CategorySyndicationDeliveryPolicyInterface
{
    /**
     * Handles the assert status workflow.
     */
    public function assertStatus(string $status): void;
    /**
     * Handles the assert attempt workflow.
     */
    public function assertAttempt(int $attempt): void;
    /**
     * Handles the normalize response message workflow.
     */
    public function normalizeResponseMessage(string $responseMessage): string;
}
