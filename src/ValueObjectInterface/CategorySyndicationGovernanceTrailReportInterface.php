<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

interface CategorySyndicationGovernanceTrailReportInterface
{
    public function destinationId(): string;

    public function categoryId(): string;

    public function mediaPolicyMode(): string;

    public function strictPublishable(): bool;

    public function fallbackPublishable(): bool;

    public function resolvedPublishable(): bool;

    public function fallbackUsed(): bool;

    public function deliveryStatus(): string;

    public function retryable(): bool;

    public function retryScheduled(): bool;

    /** @return array<string,int> */
    public function historyCounts(): array;

    /** @return list<string> */
    public function warnings(): array;

    /** @return array<string,bool> */
    public function checks(): array;
}
