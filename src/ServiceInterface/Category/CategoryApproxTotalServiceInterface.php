<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface\Category;

interface CategoryApproxTotalServiceInterface
{
    /** @return array{value:int,accuracy:string} */
    public function get(string $key, bool $withTotal): array;
}
