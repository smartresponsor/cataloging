<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

interface CategoryBreadcrumbInterface
{
    /** @return list<array{id:string, name:string, slug:string}> */
    public function chain(): array;
}
