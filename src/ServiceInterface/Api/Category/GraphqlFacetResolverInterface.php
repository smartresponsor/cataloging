<?php

declare(strict_types=1);

namespace App\ServiceInterface\Api\Category;

interface GraphqlFacetResolverInterface
{
    public function categoryFacet(array $args): array;
}
