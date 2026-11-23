<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

interface CategoryCompletenessPolicyInterface
{
    /**
     * @param array<string,mixed> $payload
     *
     * @return array<string,bool>
     */
    public function buildChecks(array $payload): array;
}
