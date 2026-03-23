<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\CategoryAccessAssignment;
use App\EntityInterface\CategoryAccessAssignmentInterface;
use App\RepositoryInterface\CategoryAccessAssignmentRepositoryInterface;
use App\ServiceInterface\CategoryAccessAssignmentServiceInterface;

final class CategoryAccessAssignmentService implements CategoryAccessAssignmentServiceInterface
{
    public function __construct(private readonly CategoryAccessAssignmentRepositoryInterface $repository)
    {
    }

    public function assignOwner(string $categoryId, string $actorUserId): CategoryAccessAssignmentInterface
    {
        return $this->assignRole($categoryId, $actorUserId, 'owner', true);
    }

    public function assignRole(string $categoryId, string $actorUserId, string $role, bool $isPrimary = false): CategoryAccessAssignmentInterface
    {
        $existing = $this->repository->findOneByCategoryIdAndActorUserId($categoryId, $actorUserId);

        if ($existing instanceof CategoryAccessAssignmentInterface) {
            if (method_exists($existing, 'activate')) {
                $existing->activate();
            }
            if (method_exists($existing, 'changeRole')) {
                $existing->changeRole($role);
            }
            if ($isPrimary) {
                $this->clearPrimaryForCategory($categoryId);
                if (method_exists($existing, 'markPrimary')) {
                    $existing->markPrimary();
                }
            }
            $this->repository->save($existing);

            return $existing;
        }

        if ($isPrimary) {
            $this->clearPrimaryForCategory($categoryId);
        }

        $assignment = CategoryAccessAssignment::create($categoryId, $actorUserId, $role, $isPrimary);
        $this->repository->save($assignment);

        return $assignment;
    }

    public function revoke(string $categoryId, string $actorUserId): void
    {
        $assignment = $this->repository->findOneByCategoryIdAndActorUserId($categoryId, $actorUserId);
        if (!$assignment instanceof CategoryAccessAssignmentInterface) {
            return;
        }

        if (method_exists($assignment, 'revoke')) {
            $assignment->revoke();
        }

        $this->repository->save($assignment);
    }

    public function setPrimary(string $categoryId, string $actorUserId): void
    {
        $assignment = $this->repository->findOneByCategoryIdAndActorUserId($categoryId, $actorUserId);
        if (!$assignment instanceof CategoryAccessAssignmentInterface) {
            return;
        }

        $this->clearPrimaryForCategory($categoryId);
        if (method_exists($assignment, 'activate')) {
            $assignment->activate();
        }
        if (method_exists($assignment, 'markPrimary')) {
            $assignment->markPrimary();
        }
        $this->repository->save($assignment);
    }

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
