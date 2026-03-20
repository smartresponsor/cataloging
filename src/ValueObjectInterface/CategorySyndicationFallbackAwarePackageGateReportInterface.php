<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ValueObjectInterface;

interface CategorySyndicationFallbackAwarePackageGateReportInterface
{
    /** @return list<string> */
    public function packageMissingRequiredFields(): array;

    /** @return list<string> */
    public function strictMediaRequiredMissing(): array;

    /** @return list<string> */
    public function fallbackMediaRequiredMissing(): array;

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
}
