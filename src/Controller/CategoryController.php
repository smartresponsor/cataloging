<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller;

use App\Cataloging\ControllerInterface\CategoryControllerInterface;
use App\Cataloging\RepositoryInterface\CategoryRepositoryInterface;
use App\Cataloging\Service\CategoryPayloadValueNormalizer;
use App\Cataloging\Service\MetaPayloadNormalizer;
use App\Cataloging\ServiceInterface\CategoryBreadcrumbBuilderInterface;
use App\Cataloging\ServiceInterface\CategoryServiceInterface as CatalogCategoryService;
use App\Cataloging\ValueObject\CategoryCreateRequest;
use App\Cataloging\ValueObject\CategoryLinkRequest;
use App\Cataloging\ValueObject\CategoryServiceMoveRequest;
use App\Cataloging\ValueObject\CategorySlugLookupRequest;
use App\Cataloging\ValueObject\CategoryTreeRequest;

/**
 * Handles the category controller application flow.
 */
/** @noinspection DuplicatedCode */
final class CategoryController implements CategoryControllerInterface
{
    private CatalogCategoryService $service;
    private CategoryRepositoryInterface $repo;
    private CategoryBreadcrumbBuilderInterface $breadcrumb;
    private MetaPayloadNormalizer $metaPayloadNormalizer;

    /**
     * Initializes the category controller service collaborators.
     */
    public function __construct(
        CatalogCategoryService $service,
        CategoryRepositoryInterface $repo,
        CategoryBreadcrumbBuilderInterface $breadcrumb,
        MetaPayloadNormalizer $metaPayloadNormalizer,
    ) {
        $this->service = $service;
        $this->repo = $repo;
        $this->breadcrumb = $breadcrumb;
        $this->metaPayloadNormalizer = $metaPayloadNormalizer;
    }

    /** @return list<array<string, mixed>> */
    public function tree(array $query, array $route): array
    {
        $taxonomy = $this->requiredString($route, 'taxonomy');
        $locale = $this->stringFromMap($query, 'locale', 'en');
        $depth = $this->intFromMap($query, 'depth', 2);
        $parentId = $this->nullableStringFromMap($query, 'parentId');
        $tree = $this->repo->tree(new CategoryTreeRequest($taxonomy, $parentId, $depth, $locale));

        return $this->listOfMaps($tree);
    }

    /** @return array{category: array<string, mixed>, breadcrumb: mixed, seo: mixed} */
    public function bySlug(array $query, array $route): array
    {
        $taxonomy = $this->requiredString($route, 'taxonomy');
        $slug = $this->requiredString($route, 'slug');
        $locale = $this->stringFromMap($query, 'locale', 'en');
        $cat = $this->repo->bySlug(new CategorySlugLookupRequest($taxonomy, $slug, $locale));
        $categoryId = $this->requiredString($cat, 'id');
        $crumb = $this->breadcrumb->build($categoryId, $locale);

        return ['category' => $cat, 'breadcrumb' => $crumb['breadcrumb'], 'seo' => $crumb['seo']];
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $route
     * @param array<string, mixed> $auth
     *
     * @return array<string, mixed>
     */
    public function create(array $body, array $route, array $auth): array
    {
        return $this->service->create(new CategoryCreateRequest(
            $this->requiredString($auth, 'actorId'),
            $this->requiredString($body, 'taxonomyId'),
            $this->nullableStringFromMap($body, 'parentId'),
            $this->stringMap($body, 'name'),
            $this->stringMap($body, 'slug'),
            $this->metaPayload($body),
        ));
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $route
     * @param array<string, mixed> $auth
     *
     * @return array<string, mixed>
     */
    public function move(array $body, array $route, array $auth): array
    {
        $actorId = $this->requiredString($auth, 'actorId');
        $categoryId = $this->requiredString($route, 'id');
        $order = $this->intFromMap($body, 'order');

        return $this->service->move(new CategoryServiceMoveRequest(
            $actorId,
            $categoryId,
            $this->nullableStringFromMap($body, 'parentId'),
            $order,
        ));
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $route
     * @param array<string, mixed> $auth
     */
    public function attach(array $body, array $route, array $auth): void
    {
        $this->service->attach($this->linkRequest($body, $route, $auth));
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $route
     * @param array<string, mixed> $auth
     */
    public function detach(array $body, array $route, array $auth): void
    {
        $this->service->detach($this->linkRequest($body, $route, $auth));
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $route
     * @param array<string, mixed> $auth
     */
    private function linkRequest(array $body, array $route, array $auth): CategoryLinkRequest
    {
        return new CategoryLinkRequest(
            $this->requiredString($auth, 'actorId'),
            $this->requiredString($route, 'id'),
            $this->requiredString($body, 'targetDomain'),
            $this->requiredString($body, 'targetClass'),
            $this->requiredString($body, 'targetId'),
        );
    }

    /** @param array<string, mixed> $map */
    private function requiredString(array $map, string $key): string
    {
        return $this->stringFromMap($map, $key);
    }

    /** @param array<string, mixed> $map */
    private function stringFromMap(array $map, string $key, string $default = ''): string
    {
        $value = $map[$key] ?? $default;

        return is_scalar($value) ? (string) $value : $default;
    }

    /**
     * @param array<string, mixed> $map
     *
     * @noinspection PhpSameParameterValueInspection
     */
    private function nullableStringFromMap(array $map, string $key): ?string
    {
        $value = $map[$key] ?? null;
        if (null === $value || '' === $value) {
            return null;
        }

        return is_scalar($value) ? (string) $value : null;
    }

    /** @param array<string, mixed> $map */
    private function intFromMap(array $map, string $key, int $default = 0): int
    {
        $value = $map[$key] ?? $default;

        return is_numeric($value) ? (int) $value : $default;
    }

    /**
     * @param array<string, mixed> $map
     *
     * @return array<string, string>
     */
    private function stringMap(array $map, string $key): array
    {
        $value = $map[$key] ?? [];
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $entryKey => $entryValue) {
            if (!is_string($entryKey) || !is_scalar($entryValue)) {
                continue;
            }
            $normalized[$entryKey] = (string) $entryValue;
        }

        return $normalized;
    }

    /**
     * @param array<string, mixed> $map
     *
     * @return array<string, array<string, bool|float|int|string|null>|bool|float|int|string|null>
     */
    private function metaPayload(array $map): array
    {
        return $this->metaPayloadNormalizer->normalize($map['meta'] ?? []);
    }

    /**
     * @param mixed $value
     *
     * @return list<array<string, mixed>>
     */
    private function listOfMaps(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $entry) {
            if (is_array($entry)) {
                $normalized[] = CategoryPayloadValueNormalizer::nestedMap($entry);
            }
        }

        return $normalized;
    }
}
