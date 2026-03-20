<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ValueObjectInterface;

interface CategoryDestinationMediaFallbackReportInterface
{
    /** @return array<string,bool> */
    public function checks(): array;

    /** @return list<string> */
    public function requiredMissing(): array;

    /** @return list<string> */
    public function warnings(): array;

    /** @return list<string> */
    public function exactMatchedBindingIds(): array;

    /** @return list<string> */
    public function fallbackMatchedBindingIds(): array;

    public function publishable(): bool;

    public function publishableWithFallback(): bool;
}
