<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CatalogCategoryAccessAssignmentEntity;
use App\Cataloging\EntityInterface\Catalog\CatalogCategoryAccessAssignmentEntityInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryAccessAssignmentRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogAccessAssignmentServiceInterface;
use App\Cataloging\ValueObject\CatalogCategoryAccessAssignmentEntityRequest;
use App\Cataloging\ValueObject\CatalogCategoryAccessAssignmentEntitySelection;
use Random\RandomException;

/**
 * Provides the catalog access assignment service application service.
 */
final readonly class CatalogAccessAssignmentService implements CatalogAccessAssignmentServiceInterface
{
    /**
     * Initializes the catalog access assignment service service collaborators.
     */
    public function __construct(private CatalogCategoryAccessAssignmentRepositoryInterface $repository)
    {
    }

    /**
     * Handles the assign owner workflow.
     */
    public function assignOwner(CatalogCategoryAccessAssignmentEntitySelection $selection): CatalogCategoryAccessAssignmentEntityInterface
    {
        return $this->assignRole(new CatalogCategoryAccessAssignmentEntityRequest(
            $selection->categoryId(),
            $selection->actorUserId(),
            'owner',
            true,
        ));
    }

    /**
     * Handles the assign role workflow.
     */
    public function assignRole(CatalogCategoryAccessAssignmentEntityRequest $request): CatalogCategoryAccessAssignmentEntityInterface
    {
        $existing = $this->repository->findOneByCategoryIdAndActorUserId(
            $request->categoryId(),
            $request->actorUserId(),
        );

        if ($existing instanceof CatalogCategoryAccessAssignmentEntityInterface) {
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

        try {
            $assignment = CatalogCategoryAccessAssignmentEntity::create(
                $request->categoryId(),
                $request->actorUserId(),
                $request->role(),
                $request->isPrimary(),
            );
        } catch (RandomException $exception) {
            throw new \RuntimeException('Unable to create category access assignment identifier.', 0, $exception);
        }
        $this->repository->save($assignment);

        return $assignment;
    }

    /**
     * Handles the revoke workflow.
     */
    public function revoke(CatalogCategoryAccessAssignmentEntitySelection $selection): void
    {
        $assignment = $this->repository->findOneByCategoryIdAndActorUserId(
            $selection->categoryId(),
            $selection->actorUserId(),
        );
        if (!$assignment instanceof CatalogCategoryAccessAssignmentEntityInterface) {
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
    public function setPrimary(CatalogCategoryAccessAssignmentEntitySelection $selection): void
    {
        $assignment = $this->repository->findOneByCategoryIdAndActorUserId(
            $selection->categoryId(),
            $selection->actorUserId(),
        );
        if (!$assignment instanceof CatalogCategoryAccessAssignmentEntityInterface) {
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
