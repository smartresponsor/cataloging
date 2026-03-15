<?php

declare(strict_types=1);

namespace App\LayerInterface\Api;

interface GraphqlFacetResolverInterface
{
    public function categoryFacet(array $args): array;
}
