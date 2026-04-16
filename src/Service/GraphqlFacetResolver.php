<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\GraphqlFacetResolverInterface;
use App\ValueObject\CategoryGraphqlFacetRequest;
use App\ValueObject\CategoryProjectionCriteria;
use Doctrine\DBAL\Exception;

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
     * @return array{
     *     items:list<array{id:string,slug:string,name:string,path:string,locale:string,score:null}>,
     *     total:int,
     * }
     *
     * @throws Exception
     */
    public function categoryFacet(CategoryGraphqlFacetRequest $request): array
    {
        $search = $this->searchService->search(CategoryProjectionCriteria::fromArray([
            'q' => $request->term(),
            'locale' => $request->locale(),
            'tenant' => $request->tenant(),
            'published' => true,
            'limit' => $request->limit(),
            'offset' => $request->offset(),
            'order' => 'name',
            'direction' => 'asc',
        ]));

        $items = [];
        $rows = $search['items'] ?? [];
        if (!is_iterable($rows)) {
            return ['items' => [], 'total' => 0];
        }

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            /** @var array<string, mixed> $row */
            $path = $this->stringValue($row, 'path');
            $pathPrefix = $request->pathPrefix();
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
}
