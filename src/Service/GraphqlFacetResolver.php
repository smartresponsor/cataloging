<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service;

use App\ServiceInterface\GraphqlFacetResolverInterface;

final class GraphqlFacetResolver implements GraphqlFacetResolverInterface
{
    private FacetSearchAdvanced $search;

    public function __construct(\PDO $pdo)
    {
        $this->search = new FacetSearchAdvanced($pdo, new FacetFilter(), new FacetRank());
    }

    public function categoryFacet(array $args): array
    {
        $term = (string) ($args['term'] ?? '');
        $locale = (string) ($args['locale'] ?? 'en');
        $pathPrefix = isset($args['pathPrefix']) ? (string) $args['pathPrefix'] : null;
        $limit = (int) ($args['limit'] ?? 20);
        $offset = (int) ($args['offset'] ?? 0);
        $rows = $this->search->search($term, $locale, $pathPrefix, $limit, $offset);
        $items = array_map(static function (array $r): array {
            return [
                'id' => (string) $r['id'],
                'slug' => (string) $r['slug'],
                'name' => (string) ($r['name'] ?? ''),
                'path' => (string) ($r['path'] ?? ''),
                'locale' => (string) ($r['locale'] ?? 'en'),
                'score' => null,
            ];
        }, $rows);

        return ['items' => $items, 'total' => count($items)];
    }
}
