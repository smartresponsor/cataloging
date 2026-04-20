<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface\Category;

/**
 * Defines the contract for category rule suggest service.
 */
interface CategoryRuleSuggestServiceInterface
{
    /** @param list<array{price:float,brand?:string,categoryId?:string}> $sample
     *  @return array<string,mixed> */
    public function suggest(array $sample): array;
}
