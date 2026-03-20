<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ValueObjectInterface;

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

    public function publishable(): bool;
}
