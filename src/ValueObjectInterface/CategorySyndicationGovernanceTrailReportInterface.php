<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

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

    public function historyCounts(): array;

    public function warnings(): array;

    public function checks(): array;
}
