<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ValueObjectInterface;

interface CategorySyndicationDestinationGovernanceSummaryInterface
{
    public function destinationId(): string;

    public function totalTrails(): int;

    public function resolvedPublishableCount(): int;

    public function fallbackUsedCount(): int;

    public function retryableCount(): int;

    public function retryScheduledCount(): int;

    public function failureTrailCount(): int;

    public function deliveredTrailCount(): int;

    public function statusCounts(): array;

    public function policyModeCounts(): array;

    public function warningCodes(): array;

    public function checks(): array;
}
