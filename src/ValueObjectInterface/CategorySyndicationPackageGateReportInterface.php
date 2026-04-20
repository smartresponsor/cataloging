<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObjectInterface;

/**
 * Defines the contract for category syndication package gate report.
 */
interface CategorySyndicationPackageGateReportInterface
{
    /** @return list<string> */
    public function packageMissingRequiredFields(): array;

    /** @return list<string> */
    public function mediaRequiredMissing(): array;

    /** @return list<string> */
    public function warnings(): array;

    /** @return array<string,bool> */
    public function checks(): array;

    /** @return list<string> */
    public function matchedBindingIds(): array;

    /**
     * Handles the publishable workflow.
     */
    public function publishable(): bool;
}
