<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

/**
 * Defines the contract for collection rule service.
 */
interface CollectionRuleServiceInterface
{
    /**
     * @param array<string,mixed> $dsl
     *
     * @return list<array<string,mixed>>
     */
    public function evaluate(array $dsl, int $limit): array;
}
