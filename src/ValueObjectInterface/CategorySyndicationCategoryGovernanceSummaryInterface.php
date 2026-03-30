<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

interface CategorySyndicationCategoryGovernanceSummaryInterface
{
    public function categoryId(): string;

    public function totalTrails(): int;

    public function resolvedPublishableCount(): int;

    public function fallbackUsedCount(): int;

    public function retryableCount(): int;

    public function retryScheduledCount(): int;

    public function failureTrailCount(): int;

    public function deliveredTrailCount(): int;

    /** @return list<string> */
    public function destinationIds(): array;

    /** @return array<string,int> */
    public function statusCounts(): array;

    /** @return array<string,int> */
    public function policyModeCounts(): array;

    /** @return list<string> */
    public function warningCodes(): array;

    /** @return array<string,bool> */
    public function checks(): array;
}
