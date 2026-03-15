<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EventInterface\Category;

interface CategoryLinkedInterface
{
    /** @return array<string,mixed> */
    public function payload(): array;
}
