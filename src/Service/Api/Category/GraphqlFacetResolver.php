<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Api\Category;

final class GraphqlFacetResolver implements GraphqlFacetResolverInterface
{
    private FacetSearchAdvanced $search;

    public function __construct(\PDO $pdo)
    {
        $this->search = new FacetSearchAdvanced($pdo, new \App\App\Service\Query\Category\FacetFilter(), new \App\App\Service\Query\Category\FacetRank());
    }

    public function categoryFacet(array $args): array
    {
        $term = (string) ($args['term'] ?? '');
        $locale = (string) ($args['locale'] ?? 'en');
        $pathPrefix = isset($args['pathPrefix']) ? (string) $args['pathPrefix'] : null;
        $limit = (int) ($args['limit'] ?? 20);
        $offset = (int) ($args['offset'] ?? 0);
        $rows = $this->search->search($term, $locale, $pathPrefix, $limit, $offset);
        $items = array_map(static function (array $row): array {
            return [
                'id' => (string) $row['id'],
                'slug' => (string) $row['slug'],
                'name' => (string) ($row['name'] ?? ''),
                'path' => (string) ($row['path'] ?? ''),
                'locale' => (string) ($row['locale'] ?? 'en'),
                'score' => null,
            ];
        }, $rows);

        return ['items' => $items, 'total' => count($items)];
    }
}
