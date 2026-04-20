<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObjectInterface;

/**
 * Defines the contract for category publication quality profile.
 */
interface CategoryPublicationQualityProfileInterface
{
    /**
     * Handles the score workflow.
     */
    public function score(): int;

    /**
     * Determines whether the publishable quality condition is satisfied.
     */
    public function isPublishableQuality(): bool;

    /**
     * Handles the risk level workflow.
     */
    public function riskLevel(): string;

    /** @return list<string> */
    public function hardBlockers(): array;

    /** @return list<string> */
    public function softWarnings(): array;

    /** @return list<string> */
    public function advisoryWarnings(): array;

    /** @return array<string,bool> */
    public function publicationChecks(): array;

    /** @return array<string,bool> */
    public function checks(): array;
}
