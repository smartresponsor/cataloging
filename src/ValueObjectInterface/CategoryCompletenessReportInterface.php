<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ValueObjectInterface;

interface CategoryCompletenessReportInterface
{
    public function score(): int;

    public function isComplete(): bool;

    /** @return list<string> */
    public function missingRequired(): array;

    /** @return list<string> */
    public function warnings(): array;

    /** @return array<string,bool> */
    public function checks(): array;

    /** @return array<string,bool> */
    public function publicationChecks(): array;
}
