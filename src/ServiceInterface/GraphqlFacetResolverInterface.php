<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;
/**
 * Defines the contract for graphql facet resolver.
 */
interface GraphqlFacetResolverInterface
{
    /**
     * @param array<string,mixed> $args
     *
     * @return array{items:list<array{id:string,slug:string,name:string,path:string,locale:string,score:null}>,total:int}
     */
    public function categoryFacet(array $args): array;
}
