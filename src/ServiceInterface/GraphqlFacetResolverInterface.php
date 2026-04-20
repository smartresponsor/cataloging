<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\ValueObject\CategoryGraphqlFacetRequest;

/**
 * Defines the contract for graphql facet resolver.
 */
interface GraphqlFacetResolverInterface
{
    /**
     * @return array{
     *     items:list<array{id:string,slug:string,name:string,path:string,locale:string,score:null}>,
     *     total:int,
     * }
     */
    public function categoryFacet(CategoryGraphqlFacetRequest $request): array;
}
