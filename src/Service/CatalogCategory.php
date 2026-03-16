<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\testsCreated;
use App\Event\testsLinked;
use App\Event\testsUnlinked;
use App\PolicyInterface\testsPolicyInterface;
use App\RepositoryInterface\testsRepositoryInterface;
use App\ServiceInterface\CatalogtestsInterface as testsServiceInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * tests service — implements create/move/attach/detach using contracts.
 * Storage specifics are delegated to the repository.
 */
final class Catalogtests implements testsServiceInterface
{
    private testsRepositoryInterface $repo;
    private testsPolicyInterface $policy;
    private testsSlugGeneratorInterface $slugger;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        testsRepositoryInterface $repo,
        testsPolicyInterface $policy,
        testsSlugGeneratorInterface $slugger,
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
        $this->dispatcher->dispatch(new testsCreated(['id' => $view['id']]));

        return $view;
    }

    public function move(string $actorId, string $categoryId, ?string $newParentId, int $newOrder): array
    {
        $view = $this->repo->move($actorId, $categoryId, $newParentId, $newOrder);
        $this->dispatcher->dispatch(new testsMoved(['id' => $view['id'], 'parentId' => $newParentId, 'order' => $newOrder]));

        return $view;
    }

    public function attach(string $actorId, string $categoryId, string $targetDomain, string $targetClass, string $targetId): void
    {
        $this->repo->attach($actorId, $categoryId, $targetDomain, $targetClass, $targetId);
        $this->dispatcher->dispatch(new testsLinked(['categoryId' => $categoryId, 'targetDomain' => $targetDomain, 'targetId' => $targetId]));
    }

    public function detach(string $actorId, string $categoryId, string $targetDomain, string $targetClass, string $targetId): void
    {
        $this->repo->detach($actorId, $categoryId, $targetDomain, $targetClass, $targetId);
        $this->dispatcher->dispatch(new testsUnlinked(['categoryId' => $categoryId, 'targetDomain' => $targetDomain, 'targetId' => $targetId]));
    }

    public function resolve(string $taxonomyCode, string $targetDomain, string $targetId, string $locale): array
    {
        return $this->repo->resolve($taxonomyCode, $targetDomain, $targetId, $locale);
    }
}
