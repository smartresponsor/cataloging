<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CategoryCreated;
use App\Cataloging\Event\CategoryLinked;
use App\Cataloging\Event\CategoryMoved;
use App\Cataloging\Event\CategoryUnlinked;
use App\Cataloging\PolicyInterface\CategoryPolicyInterface;
use App\Cataloging\RepositoryInterface\CategoryRepositoryInterface;
use App\Cataloging\ServiceInterface\CategoryInterface as CategoryCategoryServiceInterface;
use App\Cataloging\ServiceInterface\CategorySlugGeneratorInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Category service — implements create/move/attach/detach using contracts.
 * Storage specifics are delegated to the repository.
 */
final class Category implements CategoryCategoryServiceInterface
{
    private CategoryRepositoryInterface $repo;
    private CategoryPolicyInterface $policy;
    private CategorySlugGeneratorInterface $slugger;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        CategoryRepositoryInterface $repo,
        CategoryPolicyInterface $policy,
        CategorySlugGeneratorInterface $slugger,
        EventDispatcherInterface $dispatcher,
    ) {
        $this->repo = $repo;
        $this->policy = $policy;
        $this->slugger = $slugger;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param array<string, scalar|null> $name
     * @param array<string, string>      $slug
     * @param array<string, mixed>       $meta
     *
     * @return array<string, mixed>
     */
    public function create(string $actorId, string $taxonomyId, ?string $parentId, array $name, array $slug, array $meta = []): array
    {
        if (!$this->policy->canEdit($actorId, $taxonomyId, $parentId)) {
            throw new \RuntimeException('Forbidden');
        }
        $this->policy->validateSlug($slug);
        $slug = $this->slugger->generate($slug, $taxonomyId, $parentId);

        $view = $this->repo->create($taxonomyId, $parentId, $name, $slug, $meta);
        $this->dispatcher->dispatch(new CategoryCreated(['id' => $view['id']]));

        return $view;
    }

    public function move(string $actorId, string $categoryId, ?string $newParentId, int $newOrder): array
    {
        $view = $this->repo->move($actorId, $categoryId, $newParentId, $newOrder);
        $this->dispatcher->dispatch(new CategoryMoved(
            $this->stringValue($view, 'id', $categoryId),
            $this->stringValue($view, 'oldParentId'),
            (string) ($newParentId ?? ''),
            $this->stringValue($view, 'treeId', 'default'),
            $this->intValue($view, 'changedCount', 1),
        ));

        return $view;
    }

    public function attach(string $actorId, string $categoryId, string $targetDomain, string $targetClass, string $targetId): void
    {
        $this->repo->attach($actorId, $categoryId, $targetDomain, $targetClass, $targetId);
        $this->dispatcher->dispatch(new CategoryLinked(['categoryId' => $categoryId, 'targetDomain' => $targetDomain, 'targetId' => $targetId]));
    }

    public function detach(string $actorId, string $categoryId, string $targetDomain, string $targetClass, string $targetId): void
    {
        $this->repo->detach($actorId, $categoryId, $targetDomain, $targetClass, $targetId);
        $this->dispatcher->dispatch(new CategoryUnlinked(['categoryId' => $categoryId, 'targetDomain' => $targetDomain, 'targetId' => $targetId]));
    }

    public function resolve(string $taxonomyCode, string $targetDomain, string $targetId, string $locale): array
    {
        return $this->repo->resolve($taxonomyCode, $targetDomain, $targetId, $locale);
    }

    /** @param array<string, mixed> $input */
    private function stringValue(array $input, string $key, string $default = ''): string
    {
        $value = $input[$key] ?? $default;

        return is_scalar($value) ? trim((string) $value) : $default;
    }

    /** @param array<string, mixed> $input */
    private function intValue(array $input, string $key, int $default = 0): int
    {
        $value = $input[$key] ?? $default;

        return is_numeric($value) ? (int) $value : $default;
    }
}
