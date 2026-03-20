<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ValueObjectInterface;

interface CategoryDestinationMediaPolicyPreferenceInterface
{
    public function mediaPolicyMode(): string;

    /** @return array<string,bool> */
    public function checks(): array;

    /** @return list<string> */
    public function requiredMissing(): array;

    /** @return list<string> */
    public function warnings(): array;

    public function strictPublishable(): bool;

    public function fallbackPublishable(): bool;

    public function resolvedPublishable(): bool;

    public function fallbackUsed(): bool;
}
