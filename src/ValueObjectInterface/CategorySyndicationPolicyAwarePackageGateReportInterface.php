<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ValueObjectInterface;

interface CategorySyndicationPolicyAwarePackageGateReportInterface
{
    public function mediaPolicyMode(): string;

    /** @return list<string> */
    public function packageMissingRequiredFields(): array;

    /** @return list<string> */
    public function requiredMissing(): array;

    /** @return list<string> */
    public function warnings(): array;

    /** @return array<string,bool> */
    public function checks(): array;

    /** @return list<string> */
    public function exactMatchedBindingIds(): array;

    /** @return list<string> */
    public function fallbackMatchedBindingIds(): array;

    public function strictPublishable(): bool;

    public function fallbackPublishable(): bool;

    public function resolvedPublishable(): bool;

    public function fallbackUsed(): bool;
}
