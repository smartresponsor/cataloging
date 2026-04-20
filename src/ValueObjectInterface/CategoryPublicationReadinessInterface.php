<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObjectInterface;

/**
 * Defines the contract for category publication readiness.
 */
interface CategoryPublicationReadinessInterface
{
    /**
     * Determines whether the publishable condition is satisfied.
     */
    public function isPublishable(): bool;

    /**
     * Determines whether the check value is available.
     */
    public function hasCheck(string $name): bool;

    /**
     * Handles the check workflow.
     */
    public function check(string $name): bool;

    /** @return list<string> */
    public function blockers(): array;

    /** @return list<string> */
    public function warnings(): array;

    /** @return array<string,bool> */
    public function checks(): array;
}
