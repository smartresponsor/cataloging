<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\CategoryChangeRequest;
use App\Entity\CategoryReviewAssignment;
use App\Event\CategoryChangeRequestAssigned;
use App\PolicyInterface\CategoryReviewAssignmentPolicyInterface;
use App\RepositoryInterface\CategoryChangeRequestRepositoryInterface;
use App\RepositoryInterface\CategoryReviewAssignmentRepositoryInterface;
use App\ServiceInterface\CatalogReviewAssignmentServiceInterface;

final class CatalogReviewAssignmentService implements CatalogReviewAssignmentServiceInterface
{
    public function __construct(
        private readonly CategoryChangeRequestRepositoryInterface $changeRequestRepository,
        private readonly CategoryReviewAssignmentRepositoryInterface $assignmentRepository,
        private readonly CategoryReviewAssignmentPolicyInterface $policy,
    ) {
    }

    public function assign(
        string $requestId,
        string $assignedReviewer,
        string $assignedBy,
        string $priority = 'normal',
        ?\DateTimeImmutable $dueAt = null,
    ): CategoryChangeRequestAssigned {
        $request = $this->changeRequestRepository->findByRequestId($requestId);

        if (!$request instanceof CategoryChangeRequest) {
            throw new \DomainException(sprintf('Category change request not found: %s', $requestId));
        }

        $this->policy->assertCanAssign($request, $assignedReviewer, $assignedBy, $priority);

        $assignment = CategoryReviewAssignment::create(
            $request->requestId(),
            $request->categoryId(),
            $assignedReviewer,
            $assignedBy,
            $priority,
            $dueAt,
        );

        $this->assignmentRepository->save($assignment);

        return new CategoryChangeRequestAssigned(
            $assignment->requestId(),
            $assignment->categoryId(),
            $assignment->assignedReviewer(),
            $assignment->assignedBy(),
            $assignment->priority(),
            $assignment->assignedAt(),
            $assignment->dueAt(),
        );
    }
}
