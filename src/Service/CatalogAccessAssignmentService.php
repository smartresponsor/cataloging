<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\CategoryAccessAssignment;
use App\EntityInterface\CategoryAccessAssignmentInterface;
use App\RepositoryInterface\CategoryAccessAssignmentRepositoryInterface;
use App\ServiceInterface\CatalogAccessAssignmentServiceInterface;
use App\ValueObject\CategoryAccessAssignmentRequest;
use App\ValueObject\CategoryAccessAssignmentSelection;

/**
 * Provides the catalog access assignment service application service.
 */
final readonly class CatalogAccessAssignmentService implements CatalogAccessAssignmentServiceInterface
{
    /**
     * Initializes the catalog access assignment service service collaborators.
     */
    public function __construct(private CategoryAccessAssignmentRepositoryInterface $repository)
    {
    }

    /**
     * Handles the assign owner workflow.
     */
    public function assignOwner(CategoryAccessAssignmentSelection $selection): CategoryAccessAssignmentInterface
    {
        return $this->assignRole(new CategoryAccessAssignmentRequest(
            $selection->categoryId(),
            $selection->actorUserId(),
            'owner',
            true,
        ));
    }

    /**
     * Handles the assign role workflow.
     */
    public function assignRole(CategoryAccessAssignmentRequest $request): CategoryAccessAssignmentInterface
    {
        $existing = $this->repository->findOneByCategoryIdAndActorUserId(
            $request->categoryId(),
            $request->actorUserId(),
        );

        if ($existing instanceof CategoryAccessAssignmentInterface) {
            if (method_exists($existing, 'activate')) {
                $existing->activate();
            }
            if (method_exists($existing, 'changeRole')) {
                $existing->changeRole($request->role());
            }
            if ($request->isPrimary()) {
                $this->clearPrimaryForCategory($request->categoryId());
                if (method_exists($existing, 'markPrimary')) {
                    $existing->markPrimary();
                }
            }
            $this->repository->save($existing);

            return $existing;
        }

        if ($request->isPrimary()) {
            $this->clearPrimaryForCategory($request->categoryId());
        }

        $assignment = CategoryAccessAssignment::create(
            $request->categoryId(),
            $request->actorUserId(),
            $request->role(),
            $request->isPrimary(),
        );
        $this->repository->save($assignment);

        return $assignment;
    }

    /**
     * Handles the revoke workflow.
     */
    public function revoke(CategoryAccessAssignmentSelection $selection): void
    {
        $assignment = $this->repository->findOneByCategoryIdAndActorUserId(
            $selection->categoryId(),
            $selection->actorUserId(),
        );
        if (!$assignment instanceof CategoryAccessAssignmentInterface) {
            return;
        }

        if (method_exists($assignment, 'revoke')) {
            $assignment->revoke();
        }

        $this->repository->save($assignment);
    }

    /**
     * Updates the primary value.
     */
    public function setPrimary(CategoryAccessAssignmentSelection $selection): void
    {
        $assignment = $this->repository->findOneByCategoryIdAndActorUserId(
            $selection->categoryId(),
            $selection->actorUserId(),
        );
        if (!$assignment instanceof CategoryAccessAssignmentInterface) {
            return;
        }

        $this->clearPrimaryForCategory($selection->categoryId());
        if (method_exists($assignment, 'activate')) {
            $assignment->activate();
        }
        if (method_exists($assignment, 'markPrimary')) {
            $assignment->markPrimary();
        }
        $this->repository->save($assignment);
    }

    /**
     * Lists the active for category items for the current workflow.
     */
    public function listActiveForCategory(string $categoryId): array
    {
        return $this->repository->findActiveByCategoryId($categoryId);
    }

    private function clearPrimaryForCategory(string $categoryId): void
    {
        foreach ($this->repository->findActiveByCategoryId($categoryId) as $assignment) {
            if (method_exists($assignment, 'clearPrimary')) {
                $assignment->clearPrimary();
                $this->repository->save($assignment);
            }
        }
    }
}
