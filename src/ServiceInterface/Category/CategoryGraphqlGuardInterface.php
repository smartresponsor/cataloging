<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface\Category;

/**
 * Defines the contract for category graphql guard.
 */
interface CategoryGraphqlGuardInterface
{
    /** @param array<string,mixed> $ast
     *  @return array{depth:int,cost:int} */
    public function analyze(array $ast): array;
}
