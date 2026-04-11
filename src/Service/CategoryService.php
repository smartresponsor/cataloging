<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategoryCreated;
use App\Event\CategoryLinked;
use App\Event\CategoryMoved;
use App\Event\CategoryUnlinked;
use App\PolicyInterface\CategoryPolicyInterface;
use App\RepositoryInterface\CategoryRepositoryInterface;
use App\ServiceInterface\CategoryServiceInterface as CategoryCategoryServiceInterface;
use App\ServiceInterface\CategorySlugGeneratorInterface;
use App\ValueObject\CategoryCreateRequest;
use App\ValueObject\CategoryLinkRequest;
use App\ValueObject\CategoryResolveRequest;
use App\ValueObject\CategoryServiceMoveRequest;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Category service — implements create/move/attach/detach using contracts.
 * Storage specifics are delegated to the repository.
 */
final class CategoryService implements CategoryCategoryServiceInterface
{
    private CategoryRepositoryInterface $repo;
    private CategoryPolicyInterface $policy;
    private CategorySlugGeneratorInterface $slugger;
    private EventDispatcherInterface $dispatcher;

    /**
     * Initializes the category service service collaborators.
     */
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

    /** @return array<string, mixed> */
    public function create(CategoryCreateRequest $request): array
    {
        if (!$this->policy->canEdit($request->actorId(), $request->taxonomyId(), $request->parentId())) {
            throw new \RuntimeException('Forbidden');
        }
        $this->policy->validateSlug($request->slug());
        $slug = $this->slugger->generate($request->slug(), $request->taxonomyId(), $request->parentId());

        $view = $this->repo->create(
            $request->taxonomyId(),
            $request->parentId(),
            $request->name(),
            $slug,
            $request->meta(),
        );
        $this->dispatcher->dispatch(new CategoryCreated(['id' => $view['id']]));

        return $view;
    }

    /**
     * Handles the move workflow.
     */
    public function move(CategoryServiceMoveRequest $request): array
    {
        $view = $this->repo->move($request);
        $this->dispatcher->dispatch(new CategoryMoved(
            $this->stringValue($view, 'id', $request->categoryId()),
            $this->stringValue($view, 'oldParentId'),
            $request->newParentId() ?? '',
            $this->stringValue($view, 'treeId', 'default'),
            $this->intValue($view, 'changedCount', 1),
        ));

        return $view;
    }

    /**
     * Handles the attach workflow.
     */
    public function attach(CategoryLinkRequest $request): void
    {
        $this->repo->attach(
            $request->actorId(),
            $request->categoryId(),
            $request->targetDomain(),
            $request->targetClass(),
            $request->targetId(),
        );
        $this->dispatcher->dispatch(
            new CategoryLinked([
                'categoryId' => $request->categoryId(),
                'targetDomain' => $request->targetDomain(),
                'targetId' => $request->targetId(),
            ]),
        );
    }

    /**
     * Handles the detach workflow.
     */
    public function detach(CategoryLinkRequest $request): void
    {
        $this->repo->detach(
            $request->actorId(),
            $request->categoryId(),
            $request->targetDomain(),
            $request->targetClass(),
            $request->targetId(),
        );
        $this->dispatcher->dispatch(
            new CategoryUnlinked([
                'categoryId' => $request->categoryId(),
                'targetDomain' => $request->targetDomain(),
                'targetId' => $request->targetId(),
            ]),
        );
    }

    /**
     * Resolves the requested result for the provided input.
     *
     * @return list<array<string, mixed>>
     */
    public function resolve(CategoryResolveRequest $request): array
    {
        return $this->repo->resolve($request);
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
