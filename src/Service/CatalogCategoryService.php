<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\Catalog\CatalogCategoryCreatedEvent;
use App\Cataloging\Event\Catalog\CatalogCategoryLinkedEvent;
use App\Cataloging\Event\Catalog\CatalogCategoryMovedEvent;
use App\Cataloging\Event\Catalog\CatalogCategoryUnlinkedEvent;
use App\Cataloging\PolicyInterface\CategoryPolicyInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogCategoryServiceInterface as CategoryCategoryServiceInterface;
use App\Cataloging\ServiceInterface\CatalogCategorySlugGeneratorServiceInterface;
use App\Cataloging\ValueObject\CatalogCategoryLinkEntityRequest;
use App\Cataloging\ValueObject\CategoryCreateRequest;
use App\Cataloging\ValueObject\CategoryRepositoryCreateRequest;
use App\Cataloging\ValueObject\CategoryResolveRequest;
use App\Cataloging\ValueObject\CategoryServiceMoveRequest;
use App\Cataloging\ValueObject\CategorySlugGenerationRequest;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * CategoryEntity service — implements create/move/attach/detach using contracts.
 * Storage specifics are delegated to the repository.
 */
final class CatalogCategoryService implements CategoryCategoryServiceInterface
{
    private CatalogCategoryRepositoryInterface $repo;
    private CategoryPolicyInterface $policy;
    private CatalogCategorySlugGeneratorServiceInterface $slugger;
    private EventDispatcherInterface $dispatcher;

    /**
     * Initializes the category service service collaborators.
     */
    public function __construct(
        CatalogCategoryRepositoryInterface $repo,
        CategoryPolicyInterface $policy,
        CatalogCategorySlugGeneratorServiceInterface $slugger,
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
        $slug = $this->slugger->generate(new CategorySlugGenerationRequest(
            $request->slug(),
            $request->taxonomyId(),
            $request->parentId(),
        ));

        $view = $this->repo->create(new CategoryRepositoryCreateRequest(
            $request->taxonomyId(),
            $request->parentId(),
            $request->nameEntity(),
            $slug,
            $request->meta(),
        ));
        $this->dispatcher->dispatch(new CatalogCategoryCreatedEvent(['id' => $view['id']]));

        return $view;
    }

    /**
     * Handles the move workflow.
     */
    public function move(CategoryServiceMoveRequest $request): array
    {
        $view = $this->repo->move($request);
        $this->dispatcher->dispatch(new CatalogCategoryMovedEvent(
            $this->stringValue($view, 'id', $request->categoryId()),
            $this->stringValue($view, 'oldParentId'),
            $request->newParentId() ?? '',
            $this->stringValue($view, 'treeId', 'default'),
            $this->changedCountValue($view, 1),
        ));

        return $view;
    }

    /**
     * Handles the attach workflow.
     */
    public function attach(CatalogCategoryLinkEntityRequest $request): void
    {
        $this->repo->attach(
            $request->actorId(),
            $request->categoryId(),
            $request->targetDomain(),
            $request->targetClass(),
            $request->targetId(),
        );
        $this->dispatcher->dispatch(
            new CatalogCategoryLinkedEvent([
                'categoryId' => $request->categoryId(),
                'targetDomain' => $request->targetDomain(),
                'targetId' => $request->targetId(),
            ]),
        );
    }

    /**
     * Handles the detach workflow.
     */
    public function detach(CatalogCategoryLinkEntityRequest $request): void
    {
        $this->repo->detach(
            $request->actorId(),
            $request->categoryId(),
            $request->targetDomain(),
            $request->targetClass(),
            $request->targetId(),
        );
        $this->dispatcher->dispatch(
            new CatalogCategoryUnlinkedEvent([
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
    private function changedCountValue(array $input, int $default = 0): int
    {
        $value = $input['changedCount'] ?? $default;

        return is_numeric($value) ? (int) $value : $default;
    }
}
