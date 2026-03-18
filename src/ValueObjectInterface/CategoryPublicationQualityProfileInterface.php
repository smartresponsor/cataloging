<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ValueObjectInterface;

interface CategoryPublicationQualityProfileInterface
{
    public function score(): int;

    public function isPublishableQuality(): bool;

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
