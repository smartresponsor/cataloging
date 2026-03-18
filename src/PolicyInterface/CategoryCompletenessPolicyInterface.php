<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\PolicyInterface;

interface CategoryCompletenessPolicyInterface
{
    /** @param array<string,mixed> $payload */
    public function buildChecks(array $payload): array;
}
