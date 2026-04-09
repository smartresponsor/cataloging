<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\GraphqlFacetResolverInterface;

/**
 * Secondary GraphQL facet adapter over the canonical search/read services.
 *
 * GraphQL remains a compatibility/read surface rather than a first-class domain boundary.
 */
final readonly class GraphqlFacetResolver implements GraphqlFacetResolverInterface
{
    /**
     * Initializes the graphql facet resolver service collaborators.
     */
    public function __construct(private SearchService $searchService)
    {
    }

    /**
     * @param array<string,mixed> $args
     *
     * @return array{
     *     items:list<array{id:string,slug:string,name:string,path:string,locale:string,score:null}>,
     *     total:int,
     * }
     */
    public function categoryFacet(array $args): array
    {
        $term = $this->stringValue($args, 'term');
        $locale = $this->stringValue($args, 'locale', 'en');
        $tenant = $this->nullableStringValue($args, 'tenant');
        $pathPrefix = $this->nullableStringValue($args, 'pathPrefix');
        $limit = $this->intValue($args, 'limit', 20);
        $offset = $this->intValue($args, 'offset', 0);
        $search = $this->searchService->search([
            'q' => $term,
            'locale' => $locale,
            'tenant' => $tenant,
            'published' => true,
            'limit' => $limit,
            'offset' => $offset,
            'order' => 'name',
            'direction' => 'asc',
        ]);

        $items = [];
        foreach ($search['items'] as $row) {
            $path = $this->stringValue($row, 'path');
            if (null !== $pathPrefix && '' !== $pathPrefix && !str_starts_with($path, $pathPrefix)) {
                continue;
            }

            $items[] = [
                'id' => $this->stringValue($row, 'id'),
                'slug' => $this->stringValue($row, 'slug'),
                'name' => $this->stringValue($row, 'name'),
                'path' => $path,
                'locale' => $this->stringValue($row, 'locale', 'en'),
                'score' => null,
            ];
        }

        return ['items' => $items, 'total' => count($items)];
    }

    /** @param array<string,mixed> $input */
    private function stringValue(array $input, string $key, string $default = ''): string
    {
        $value = $input[$key] ?? $default;

        return is_scalar($value) ? trim((string) $value) : $default;
    }

    /** @param array<string,mixed> $input */
    private function nullableStringValue(array $input, string $key): ?string
    {
        if (!array_key_exists($key, $input) || null === $input[$key]) {
            return null;
        }
        $value = $input[$key];

        return is_scalar($value) ? trim((string) $value) : null;
    }

    /** @param array<string,mixed> $input */
    private function intValue(array $input, string $key, int $default): int
    {
        $value = $input[$key] ?? $default;

        return is_numeric($value) ? (int) $value : $default;
    }
}
