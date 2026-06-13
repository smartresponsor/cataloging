<?php

declare(strict_types=1);

namespace App\Cataloging\Service\Catalog;

use App\Cataloging\Value\Surface\CatalogSurfaceContract;

final class CatalogSurfaceContractFactory
{
    /**
     * @param list<array<string, mixed>> $categories
     */
    public function create(string $catalogToken, array $categories, array $filters = [], string $query = ''): CatalogSurfaceContract
    {
        $sections = [
            [
                'id' => 'all',
                'title' => 'All categories',
                'summary' => sprintf('%d categories discovered in the catalog.', count($categories)),
                'cards' => $this->createCards($categories),
            ],
        ];

        return new CatalogSurfaceContract(
            CatalogSurfaceContract::WORD,
            CatalogSurfaceContract::VIEW_BASE,
            '@Interfacing/catalog/index.html.twig',
            $this->slotMap(),
            $catalogToken,
            [
                'query' => $query,
                'top.search' => [
                    'action' => '/catalog/',
                    'method' => 'GET',
                    'queryName' => 'q',
                    'placeholder' => 'Search categories, products, collections...',
                    'query' => $query,
                ],
                'left.panel' => [
                    'filters' => $this->createFilters($filters),
                ],
                'main.body' => [
                    'sections' => $sections,
                ],
                'right.panel' => [
                    'stats' => $this->createStats($categories),
                    'actions' => $this->createHeroActions(),
                ],
            ],
        );
    }

    /**
     * @param array<string, mixed> $filters
     *
     * @return list<array{id: string, title: string, url: string}>
     */
    private function createFilters(array $filters): array
    {
        $normalized = [];
        foreach ($filters as $key => $value) {
            if (!is_scalar($value) && !is_array($value)) {
                continue;
            }
            $normalized[] = [
                'id' => is_scalar($key) ? (string) $key : 'filter',
                'title' => is_scalar($value) ? (string) $value : (string) $key,
                'url' => '/catalog/?'.http_build_query(['q' => is_scalar($value) ? (string) $value : (string) $key]),
            ];
        }

        if ([] === $normalized) {
            $normalized = [
                ['id' => 'all', 'title' => 'All categories', 'url' => '/catalog/'],
                ['id' => 'published', 'title' => 'Published', 'url' => '/catalog/?published=1'],
                ['id' => 'draft', 'title' => 'Drafts', 'url' => '/catalog/?published=0'],
            ];
        }

        return $normalized;
    }

    /**
     * @param list<array<string, mixed>> $categories
     *
     * @return list<array<string, mixed>>
     */
    private function createCards(array $categories): array
    {
        $cards = [];

        foreach ($categories as $category) {
            $nameEntity = (string) ($category['nameEntity'] ?? $category['slug'] ?? 'CategoryEntity');
            $slug = (string) ($category['slug'] ?? strtolower(str_replace(' ', '-', $nameEntity)));
            $cards[] = [
                'id' => (string) ($category['id'] ?? $slug),
                'title' => $nameEntity,
                'eyebrow' => (string) ($category['workflow_state'] ?? 'CategoryEntity'),
                'summary' => sprintf(
                    '%s · %s',
                    (string) ($category['locale'] ?? 'global'),
                    (string) ($category['published'] ? 'published' : 'draft')
                ),
                'href' => '/catalog/category/'.rawurlencode($slug),
                'status' => (bool) ($category['published'] ?? false) ? 'Available' : 'Draft',
                'itemCount' => (string) (isset($category['path']) ? 1 : 0).' item',
                'tags' => array_values(array_filter([
                    (string) ($category['tenant'] ?? ''),
                    (string) ($category['locale'] ?? ''),
                    (string) ($category['workflow_state'] ?? ''),
                ])),
            ];
        }

        return $cards;
    }

    /**
     * @param list<array<string, mixed>> $categories
     *
     * @return array<int, array{label: string, value: string}>
     */
    private function createStats(array $categories): array
    {
        $published = 0;
        $tenants = [];
        $locales = [];

        foreach ($categories as $category) {
            if (!empty($category['published'])) {
                ++$published;
            }
            if (is_scalar($category['tenant'] ?? null) && '' !== (string) $category['tenant']) {
                $tenants[(string) $category['tenant']] = true;
            }
            if (is_scalar($category['locale'] ?? null) && '' !== (string) $category['locale']) {
                $locales[(string) $category['locale']] = true;
            }
        }

        return [
            ['label' => 'Categories', 'value' => (string) count($categories)],
            ['label' => 'Published', 'value' => (string) $published],
            ['label' => 'Tenants', 'value' => (string) count($tenants)],
            ['label' => 'Locales', 'value' => (string) count($locales)],
        ];
    }

    /**
     * @return list<array{title: string, url: string}>
     */
    private function createHeroActions(): array
    {
        return [
            ['title' => 'Browse catalog', 'url' => '/catalog/'],
            ['title' => 'CategoryEntity list', 'url' => '/catalog/category/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function slotMap(): array
    {
        return [
            'top.search' => 'Search',
            'left.panel' => 'Filters',
            'main.body' => 'Sections',
            'right.panel' => 'Stats',
        ];
    }
}
