<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

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
