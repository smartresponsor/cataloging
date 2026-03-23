<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

interface GraphqlFacetResolverInterface
{
    public function categoryFacet(array $args): array;
}
