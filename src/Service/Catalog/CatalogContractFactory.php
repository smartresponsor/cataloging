<?php

declare(strict_types=1);

namespace App\Cataloging\Service\Catalog;

use App\Cataloging\Value\Surface\CatalogContract;

final class CatalogContractFactory
{
    /**
     * @param list<array<string, mixed>> $categories
     * @param array<string, mixed>       $filters
     */
    public function create(string $catalogToken, array $categories, array $filters = [], string $query = ''): CatalogContract
    {
        $query = trim($query);
        $flattened = $this->flatten($categories);
        $catalogCards = $this->createCatalogCards($flattened);
        $resultCards = '' === $query ? [] : $this->createCards($flattened);

        $sections = [[
            'id' => 'catalogs',
            'title' => 'Explore catalogs',
            'summary' => 'Choose what you want to request, order, buy, or offer.',
            'cards' => $catalogCards,
        ]];

        if ('' !== $query) {
            $sections[] = [
                'id' => 'search-results',
                'title' => 'Search results',
                'summary' => sprintf('%d matching catalog sections.', count($resultCards)),
                'cards' => $resultCards,
            ];
        }

        return new CatalogContract(
            CatalogContract::WORD,
            CatalogContract::VIEW_BASE,
            '@Interfacing/catalog/index.html.twig',
            $this->slotMap(),
            $catalogToken,
            [
                'query' => $query,
                'top.search' => [
                    'action' => '/catalog/',
                    'method' => 'GET',
                    'queryName' => 'q',
                    'placeholder' => 'Search tasks, orders, products, and services...',
                    'query' => $query,
                ],
                'left.panel' => [
                    'filters' => $this->createFilters($catalogCards),
                ],
                'main.body' => [
                    'title' => 'Marketplace',
                    'description' => 'One marketplace for customer requests, packaged orders, physical products, and provider services.',
                    'sections' => $sections,
                ],
                'right.panel' => [
                    'stats' => $this->createStats($flattened, $catalogCards),
                    'actions' => $this->createHeroActions(),
                ],
            ],
        );
    }

    /**
     * @param list<array<string, mixed>> $tree
     */
    public function createDetail(string $catalogToken, array $tree, string $slug): ?CatalogContract
    {
        $path = $this->findNodePath($tree, trim($slug));
        if ([] === $path) {
            return null;
        }

        $category = $path[array_key_last($path)];
        $catalogKind = $this->pathCatalogKind($path);
        $title = trim((string) ($category['nameEntity'] ?? $category['slug'] ?? 'Catalog section'));
        $imageUrl = trim((string) ($category['imageUrl'] ?? $category['iconUrl'] ?? $category['icon_url'] ?? ''));
        $children = array_values(array_filter($category['children'] ?? [], 'is_array'));
        $childCards = [];

        foreach ($children as $child) {
            $childCards[] = $this->card(
                $child,
                $this->detailDescription($catalogKind, $child),
                $this->catalogEyebrow($catalogKind ?? 'category'),
                $catalogKind,
            );
        }

        $sections = [[
            'id' => [] === $childCards ? 'leaf' : 'categories',
            'title' => [] === $childCards ? 'Explore this category' : 'Choose a category',
            'summary' => [] === $childCards
                ? 'Listings and marketplace offers for this category can be discovered from the catalog search.'
                : sprintf('%d %s available in %s.', count($childCards), 1 === count($childCards) ? 'category' : 'categories', $title),
            'cards' => $childCards,
        ]];

        return new CatalogContract(
            CatalogContract::WORD,
            CatalogContract::VIEW_BASE,
            '@Interfacing/catalog/index.html.twig',
            $this->detailSlotMap(),
            $catalogToken,
            [
                'query' => '',
                'top.search' => [
                    'action' => '/catalog/',
                    'method' => 'GET',
                    'queryName' => 'q',
                    'placeholder' => sprintf('Search in %s...', $title),
                    'query' => '',
                ],
                'left.panel' => [
                    'filters' => $this->breadcrumbFilters($path),
                ],
                'main.body' => [
                    'title' => $title,
                    'description' => $this->detailDescription($catalogKind, $category),
                    'eyebrow' => $this->catalogEyebrow($catalogKind ?? 'category'),
                    'imageUrl' => '' === $imageUrl ? null : $imageUrl,
                    'image' => '' === $imageUrl ? null : $imageUrl,
                    'breadcrumbs' => $this->breadcrumbs($path),
                    'sections' => $sections,
                ],
                'right.panel' => [
                    'stats' => [
                        ['label' => 'Categories', 'value' => (string) count($children)],
                        ['label' => 'Type', 'value' => ucfirst($catalogKind ?? 'category')],
                        ['label' => 'Status', 'value' => !empty($category['published']) ? 'Available' : 'Unavailable'],
                    ],
                    'actions' => $this->detailActions($catalogKind, $title),
                ],
            ],
        );
    }

    /**
     * @param list<array<string, mixed>> $nodes
     * @return list<array<string, mixed>>
     */
    private function flatten(array $nodes): array
    {
        $result = [];
        foreach ($nodes as $node) {
            $result[] = $node;
            $children = $node['children'] ?? null;
            if (is_array($children)) {
                $result = [...$result, ...$this->flatten(array_values(array_filter($children, 'is_array')))];
            }
        }

        return $result;
    }

    /**
     * @param list<array<string, mixed>> $nodes
     * @return list<array<string, mixed>>
     */
    private function findNodePath(array $nodes, string $slug): array
    {
        foreach ($nodes as $node) {
            if (trim((string) ($node['slug'] ?? '')) === $slug) {
                return [$node];
            }

            $children = array_values(array_filter($node['children'] ?? [], 'is_array'));
            $childPath = $this->findNodePath($children, $slug);
            if ([] !== $childPath) {
                return [$node, ...$childPath];
            }
        }

        return [];
    }

    /**
     * @param list<array<string, mixed>> $path
     */
    private function pathCatalogKind(array $path): ?string
    {
        foreach ($path as $node) {
            $kind = $this->catalogKind((string) ($node['nameEntity'] ?? ''));
            if (null !== $kind) {
                return $kind;
            }
        }

        return null;
    }

    /**
     * @param list<array<string, mixed>> $categories
     * @return list<array<string, mixed>>
     */
    private function createCatalogCards(array $categories): array
    {
        $cards = [];
        foreach ($categories as $category) {
            $name = trim((string) ($category['nameEntity'] ?? ''));
            $kind = $this->catalogKind($name);
            if (null === $kind) {
                continue;
            }

            $cards[] = $this->card($category, $this->catalogDescription($kind), $this->catalogEyebrow($kind), $kind);
        }

        usort($cards, fn (array $left, array $right): int => $this->catalogOrder((string) ($left['kind'] ?? '')) <=> $this->catalogOrder((string) ($right['kind'] ?? '')));

        return $cards;
    }

    /**
     * @param list<array<string, mixed>> $categories
     * @return list<array<string, mixed>>
     */
    private function createCards(array $categories): array
    {
        $cards = [];
        foreach ($categories as $category) {
            $name = trim((string) ($category['nameEntity'] ?? $category['slug'] ?? 'Catalog section'));
            if ('Marketplace' === $name) {
                continue;
            }

            $kind = $this->catalogKind($name) ?? 'category';
            $cards[] = $this->card(
                $category,
                $this->genericDescription($category),
                $this->catalogEyebrow($kind),
                'category' === $kind ? null : $kind,
            );
        }

        return $cards;
    }

    /**
     * @param array<string, mixed> $category
     * @return array<string, mixed>
     */
    private function card(array $category, string $summary, string $eyebrow, ?string $inheritedKind = null): array
    {
        $name = trim((string) ($category['nameEntity'] ?? $category['slug'] ?? 'Catalog section'));
        $slug = trim((string) ($category['slug'] ?? ''));
        $id = (string) ($category['id'] ?? $slug);
        $imageUrl = trim((string) ($category['imageUrl'] ?? $category['iconUrl'] ?? $category['icon_url'] ?? ''));
        $kind = $inheritedKind ?? $this->catalogKind($name) ?? 'category';

        return [
            'id' => $id,
            'kind' => $kind,
            'title' => $name,
            'eyebrow' => $eyebrow,
            'summary' => $summary,
            'href' => '/catalog/category/'.rawurlencode($slug),
            'status' => 'Available',
            'itemCount' => $this->childCountLabel($category),
            'imageUrl' => '' === $imageUrl ? null : $imageUrl,
            'image' => '' === $imageUrl ? null : $imageUrl,
            'tags' => $this->businessTags($kind),
        ];
    }

    /**
     * @param list<array<string, mixed>> $catalogCards
     * @return list<array{id: string, title: string, url: string}>
     */
    private function createFilters(array $catalogCards): array
    {
        $items = [['id' => 'all', 'title' => 'All catalogs', 'url' => '/catalog/']];
        foreach ($catalogCards as $card) {
            $items[] = [
                'id' => (string) ($card['kind'] ?? 'catalog'),
                'title' => (string) ($card['title'] ?? 'Catalog'),
                'url' => (string) ($card['href'] ?? '/catalog/'),
            ];
        }

        return $items;
    }

    /**
     * @param list<array<string, mixed>> $path
     * @return list<array{id: string, title: string, url: string}>
     */
    private function breadcrumbFilters(array $path): array
    {
        $filters = [['id' => 'marketplace', 'title' => 'Marketplace', 'url' => '/catalog/']];
        foreach ($path as $node) {
            $name = trim((string) ($node['nameEntity'] ?? ''));
            $slug = trim((string) ($node['slug'] ?? ''));
            if ('Marketplace' === $name || '' === $name || '' === $slug) {
                continue;
            }

            $filters[] = [
                'id' => $slug,
                'title' => $name,
                'url' => '/catalog/category/'.rawurlencode($slug),
            ];
        }

        return $filters;
    }

    /**
     * @param list<array<string, mixed>> $path
     * @return list<array{title: string, url: string}>
     */
    private function breadcrumbs(array $path): array
    {
        $items = [['title' => 'Marketplace', 'url' => '/catalog/']];
        foreach ($path as $node) {
            $name = trim((string) ($node['nameEntity'] ?? ''));
            $slug = trim((string) ($node['slug'] ?? ''));
            if ('Marketplace' === $name || '' === $name || '' === $slug) {
                continue;
            }

            $items[] = ['title' => $name, 'url' => '/catalog/category/'.rawurlencode($slug)];
        }

        return $items;
    }

    /**
     * @param list<array<string, mixed>> $categories
     * @param list<array<string, mixed>> $catalogCards
     * @return array<int, array{label: string, value: string}>
     */
    private function createStats(array $categories, array $catalogCards): array
    {
        $published = 0;
        foreach ($categories as $category) {
            if (!empty($category['published'])) {
                ++$published;
            }
        }

        return [
            ['label' => 'Catalogs', 'value' => (string) count($catalogCards)],
            ['label' => 'Categories', 'value' => (string) max(0, count($categories) - 1)],
            ['label' => 'Available', 'value' => (string) $published],
        ];
    }

    /**
     * @return list<array{title: string, url: string}>
     */
    private function createHeroActions(): array
    {
        return [
            ['title' => 'Browse marketplace', 'url' => '/catalog/'],
            ['title' => 'Find services', 'url' => '/catalog/?q=service'],
        ];
    }

    /**
     * @return list<array{title: string, url: string}>
     */
    private function detailActions(?string $kind, string $title): array
    {
        $query = match ($kind) {
            'task' => 'task',
            'order' => 'order',
            'product' => 'product',
            'service' => 'service',
            default => $title,
        };
        $primary = match ($kind) {
            'task' => 'Browse task requests',
            'order' => 'Browse orders',
            'product' => 'Browse products',
            'service' => 'Find services',
            default => 'Search marketplace',
        };

        return [
            ['title' => $primary, 'url' => '/catalog/?'.http_build_query(['q' => $query])],
            ['title' => 'Back to marketplace', 'url' => '/catalog/'],
        ];
    }

    /** @param array<string, mixed> $category */
    private function genericDescription(array $category): string
    {
        $path = trim((string) ($category['path'] ?? ''));
        $depth = '' === $path ? 0 : substr_count($path, '.');

        return 0 < $depth
            ? 'Browse this marketplace category and continue to related listings.'
            : 'Explore this marketplace catalog.';
    }

    /** @param array<string, mixed> $category */
    private function detailDescription(?string $kind, array $category): string
    {
        $name = trim((string) ($category['nameEntity'] ?? 'this category'));
        if (null !== $kind && null !== $this->catalogKind($name)) {
            return $this->catalogDescription($kind);
        }

        return match ($kind) {
            'task' => sprintf('Browse customer-requested work in %s and related job categories.', $name),
            'order' => sprintf('Explore ready-to-request work packages and projects in %s.', $name),
            'product' => sprintf('Shop marketplace products, parts, tools, and materials in %s.', $name),
            'service' => sprintf('Discover provider-authored professional services in %s.', $name),
            default => sprintf('Explore %s in the marketplace.', $name),
        };
    }

    /** @param array<string, mixed> $category */
    private function childCountLabel(array $category): string
    {
        $children = $category['children'] ?? null;
        if (is_array($children)) {
            $count = count($children);

            return sprintf('%d %s', $count, 1 === $count ? 'category' : 'categories');
        }

        return 'Browse';
    }

    /** @return list<string> */
    private function businessTags(string $kind): array
    {
        return match ($kind) {
            'task' => ['Customer request', 'Work'],
            'order' => ['Order', 'Project'],
            'product' => ['Marketplace', 'Goods'],
            'service' => ['Provider offer', 'Service'],
            default => ['Category'],
        };
    }

    private function catalogKind(string $name): ?string
    {
        return match (strtolower(trim($name))) {
            'task catalog' => 'task',
            'order catalog' => 'order',
            'product catalog' => 'product',
            'service catalog' => 'service',
            default => null,
        };
    }

    private function catalogDescription(string $kind): string
    {
        return match ($kind) {
            'task' => 'Customer requests for work that local professionals can accept and complete.',
            'order' => 'Ready-to-request job packages and multi-step projects with a defined scope.',
            'product' => 'Tools, parts, fixtures, equipment, and other physical marketplace goods.',
            'service' => 'Professional services published by providers for customers to discover and order.',
            default => 'Browse marketplace categories.',
        };
    }

    private function catalogEyebrow(string $kind): string
    {
        return match ($kind) {
            'task' => 'Request work',
            'order' => 'Order a project',
            'product' => 'Shop products',
            'service' => 'Hire a professional',
            default => 'Marketplace category',
        };
    }

    private function catalogOrder(string $kind): int
    {
        return match ($kind) {
            'task' => 10,
            'order' => 20,
            'product' => 30,
            'service' => 40,
            default => 100,
        };
    }

    /** @return array<string, string> */
    private function slotMap(): array
    {
        return [
            'top.search' => 'Search',
            'left.panel' => 'Catalogs',
            'main.body' => 'Marketplace',
            'right.panel' => 'Overview',
        ];
    }

    /** @return array<string, string> */
    private function detailSlotMap(): array
    {
        return [
            'top.search' => 'Search',
            'left.panel' => 'Browse',
            'main.body' => 'Category',
            'right.panel' => 'Actions',
        ];
    }
}
