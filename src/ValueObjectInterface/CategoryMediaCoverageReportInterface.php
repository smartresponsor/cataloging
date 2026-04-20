<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObjectInterface;

/**
 * Defines the contract for category media coverage report.
 */
interface CategoryMediaCoverageReportInterface
{
    /** @return array<string,bool> */
    public function checks(): array;

    /** @return list<string> */
    public function requiredMissing(): array;

    /** @return list<string> */
    public function warnings(): array;

    /**
     * Handles the media ready workflow.
     */
    public function mediaReady(): bool;

    /**
     * Handles the banner ready workflow.
     */
    public function bannerReady(): bool;

    /**
     * Handles the required coverage ready workflow.
     */
    public function requiredCoverageReady(): bool;
}
