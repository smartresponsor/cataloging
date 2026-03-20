<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategoryDestinationMediaPolicyPreferenceInterface;

interface CategoryDestinationMediaPolicyPreferencePolicyInterface
{
    /**
     * @param array<string,mixed> $strictPayload
     * @param array<string,mixed> $fallbackPayload
     */
    public function buildReport(string $mediaPolicyMode, array $strictPayload, array $fallbackPayload): CategoryDestinationMediaPolicyPreferenceInterface;
}
