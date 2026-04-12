<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Category;

/**
 * Defines the contract for category loader.
 */
interface CategoryLoaderInterface
{
    /** @param list<string> $ids
     *  @return list<array{id:string,name:string,slug:string}> */
    public function load(array $ids): array;
}
