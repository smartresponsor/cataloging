<?php

declare(strict_types=1);

namespace App\Cataloging\Service\Catalog;

use App\Cataloging\Value\Surface\CatalogSurfaceContract;

final class CatalogSurfaceContractFactory
{
    /**
     * @param list<array<string, mixed>> $categories
     * @param array<string, mixed>       $filters
     */
    public function create(string $catalogToken, array $categories, array $filters = [], string $query = ''): CatalogSurfaceContract
    {
        $query = trim($query);
        $catalogCards = $this->createCatalogCards($categories);
        $resultCards = '' === $query ? [] : $this->createCards($categories);
        $sections = [
            [
                'id' => 'catalogs',
                'title' => 'Explore catalogs',
                'summary' => 'Choose what you want to request, order, buy, or offer.',
                'cards' => $catalogCards,
            ],
        ];

        if ('' !== $query) {
            $sections[] = [
                'id' => 'search-results',
                'title' => 'Search results',
                'summary' => sprintf('%d matching catalog sections.', count($resultCards)),
                'cards' => $resultCards,
            ];
        }

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
                    'placeholder' => 'Search tasks, orders, products, and services...',
                    'query' => $query,
                ],
                'left.panel' => [
                    'filters' => $this->createFilters($catalogCards, $filters),
                ],
                'main.body' => [
                    'title' => 'Marketplace',
                    'description' => 'One marketplace for customer requests, packaged orders, physical products, and provider services.',
                    'sections' => $sections,
                ],
                'right.panel' => [
                    'stats' => $this->createStats($categories, $catalogCards),
                    'actions' => $this->createHeroActions(),
                ],
            ],
        );
    }

    /**
     * @param list<array<string, mixed>> $categories
     *
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

            $cards[] = $this->card($category, $this->catalogDescription($kind), $this->catalogEyebrow($kind));
        }

        usort($cards, fn (array $left, array $right): int => $this->catalogOrder((string) ($left['kind'] ?? '')) <=> $this->catalogOrder((string) ($right['kind'] ?? '')));

        return $cards;
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
            $name = trim((string) ($category['nameEntity'] ?? $category['slug'] ?? 'Catalog section'));
            if ('Marketplace' === $name) {
                continue;
            }

            $cards[] = $this->card(
                $category,
                $this->genericDescription($category),
                $this->catalogEyebrow($this->catalogKind($name) ?? 'category'),
            );
        }

        return $cards;
    }

    /**
     * @param array<string, mixed> $category
     *
     * @return array<string, mixed>
     */
    private function card(array $category, string $summary, string $eyebrow): array
    {
        $name = trim((string) ($category['nameEntity'] ?? $category['slug'] ?? 'Catalog section'));
        $slug = trim((string) ($category['slug'] ?? ''));
        $id = (string) ($category['id'] ?? $slug);
        $imageUrl = trim((string) ($category['imageUrl'] ?? $category['iconUrl'] ?? $category['icon_url'] ?? ''));
        $kind = $this->catalogKind($name) ?? 'category';

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
     * @param array<string, mixed>       $filters
     *
     * @return list<array{id: string, title: string, url: string}>
     */
    private function createFilters(array $catalogCards, array $filters): array
    {
        $items = [['id' => 'all', 'title' => 'All catalogs', 'url' => '/catalog/']];
        foreach ($catalogCards as $card) {
            $title = (string) ($card['title'] ?? 'Catalog');
            $items[] = [
                'id' => (string) ($card['kind'] ?? 'catalog'),
                'title' => $title,
                'url' => (string) ($card['href'] ?? '/catalog/'),
            ];
        }

        return $items;
    }

    /**
     * @param list<array<string, mixed>> $categories
     * @param list<array<string, mixed>> $catalogCards
     *
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
     * @param array<string, mixed> $category
     */
    private function genericDescription(array $category): string
    {
        $path = trim((string) ($category['path'] ?? ''));
        $depth = '' === $path ? 0 : substr_count($path, '.');

        return 0 < $depth
            ? 'Browse this marketplace category and continue to related listings.'
            : 'Explore this marketplace catalog.';
    }

    /**
     * @param array<string, mixed> $category
     */
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
        $normalized = strtolower(trim($name));

        return match ($normalized) {
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

    /**
     * @return array<string, string>
     */
    private function slotMap(): array
    {
        return [
            'top.search' => 'Search',
            'left.panel' => 'Catalogs',
            'main.body' => 'Marketplace',
            'right.panel' => 'Overview',
        ];
    }
}
