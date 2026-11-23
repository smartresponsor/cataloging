<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service\Governance;

use App\Cataloging\Projection\CategoryGovernanceView;
use App\Cataloging\RepositoryInterface\CategoryAccessAssignmentRepositoryInterface;
use App\Cataloging\Service\Security\CategoryRole;
use App\Cataloging\ServiceInterface\Governance\CategoryGovernanceViewBuilderInterface;

final class CategoryGovernanceViewBuilder implements CategoryGovernanceViewBuilderInterface
{
    public function __construct(private readonly CategoryAccessAssignmentRepositoryInterface $assignmentRepository)
    {
    }

    public function build(string $categoryId): CategoryGovernanceView
    {
        $primary = $this->assignmentRepository->findPrimaryForCategoryId($categoryId);
        $assignments = [];

        foreach ($this->assignmentRepository->findActiveByCategoryId($categoryId) as $assignment) {
            $assignments[] = [
                'assignmentId' => $assignment->assignmentId(),
                'actorUserId' => $assignment->actorUserId(),
                'role' => $assignment->role(),
                'status' => $assignment->status(),
                'isPrimary' => $assignment->isPrimary(),
                'grantedAt' => $assignment->grantedAt()->format(DATE_ATOM),
                'revokedAt' => $assignment->revokedAt()?->format(DATE_ATOM),
            ];
        }

        return new CategoryGovernanceView(
            categoryId: trim($categoryId),
            primaryActorUserId: $primary?->actorUserId(),
            activeAssignments: $assignments,
            roleCapabilities: $this->roleCapabilities(),
            generatedAt: (new \DateTimeImmutable('now'))->format(DATE_ATOM),
        );
    }

    /** @return array<string,bool> */
    private function roleCapabilities(): array
    {
        return [
            'owner' => true,
            'editor' => true,
            'publisher' => true,
            'reviewer' => true,
            'admin' => true,
            'ROLE_ADMIN' => true,
            'ROLE_SUPER_ADMIN' => true,
            CategoryRole::OWNER => true,
            CategoryRole::EDITOR => true,
            CategoryRole::PUBLISHER => true,
        ];
    }
}
