<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\CatalogCategoryChangeRequest;
use App\Entity\CategoryReviewAssignment;
use App\Event\CategoryChangeRequestAssigned;
use App\PolicyInterface\CategoryReviewAssignmentPolicyInterface;
use App\RepositoryInterface\CategoryChangeRequestRepositoryInterface;
use App\RepositoryInterface\CategoryReviewAssignmentRepositoryInterface;
use App\ServiceInterface\CatalogReviewAssignmentServiceInterface;
use App\ValueObject\CategoryReviewAssignmentRequest;

/**
 * Provides the catalog review assignment service application service.
 */
final readonly class CatalogReviewAssignmentService implements CatalogReviewAssignmentServiceInterface
{
    /**
     * Initializes the catalog review assignment service service collaborators.
     */
    public function __construct(
        private CategoryChangeRequestRepositoryInterface $changeRequestRepository,
        private CategoryReviewAssignmentRepositoryInterface $assignmentRepository,
        private CategoryReviewAssignmentPolicyInterface $policy,
    ) {
    }

    /**
     * Handles the assign workflow.
     */
    public function assign(CategoryReviewAssignmentRequest $request): CategoryChangeRequestAssigned
    {
        $changeRequest = $this->changeRequestRepository->findByRequestId($request->requestId());

        if (!$changeRequest instanceof CatalogCategoryChangeRequest) {
            throw new \DomainException(sprintf('Category change request not found: %s', $request->requestId()));
        }

        $this->policy->assertCanAssign(
            $changeRequest,
            $request->assignedReviewer(),
            $request->assignedBy(),
            $request->priority(),
        );

        $assignment = CategoryReviewAssignment::create(
            $changeRequest->requestId(),
            $changeRequest->categoryId(),
            $request->assignedReviewer(),
            $request->assignedBy(),
            $request->priority(),
            $request->dueAt(),
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
