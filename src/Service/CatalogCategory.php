<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\Category\CategoryCreated;
use App\Event\Category\CategoryLinked;
use App\Event\Category\CategoryMoved;
use App\Event\Category\CategoryUnlinked;
use App\PolicyInterface\Category\CategoryPolicyInterface;
use App\RepositoryInterface\Category\CategoryRepositoryInterface;
use App\ServiceInterface\CatalogCategoryInterface as CategoryServiceInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Category service — implements create/move/attach/detach using contracts.
 * Storage specifics are delegated to the repository.
 */
final class CatalogCategory implements CategoryServiceInterface
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
        $this->dispatcher->dispatch(new CategoryMoved(['id' => $view['id'], 'parentId' => $newParentId, 'order' => $newOrder]));

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
}
