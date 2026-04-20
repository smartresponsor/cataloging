<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

/**
 * Defines the contract for category completeness policy.
 */
interface CategoryCompletenessPolicyInterface
{
    /**
     * @param array<string,mixed> $payload
     *
     * @return array<string,bool>
     */
    public function buildChecks(array $payload): array;
}
