<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

interface CategoryCompletenessPolicyInterface
{
    /** @param array<string,mixed> $payload */
    public function buildChecks(array $payload): array;
}
