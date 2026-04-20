<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObjectInterface;

/**
 * Defines the contract for category destination media policy preference.
 */
interface CategoryDestinationMediaPolicyPreferenceInterface
{
    /**
     * Handles the media policy mode workflow.
     */
    public function mediaPolicyMode(): string;

    /** @return array<string,bool> */
    public function checks(): array;

    /** @return list<string> */
    public function requiredMissing(): array;

    /** @return list<string> */
    public function warnings(): array;

    /**
     * Handles the strict publishable workflow.
     */
    public function strictPublishable(): bool;

    /**
     * Handles the fallback publishable workflow.
     */
    public function fallbackPublishable(): bool;

    /**
     * Resolves the d publishable result for the current workflow.
     */
    public function resolvedPublishable(): bool;

    /**
     * Handles the fallback used workflow.
     */
    public function fallbackUsed(): bool;
}
