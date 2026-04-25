<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CatalogCategoryChangeRequestEntity;
use App\Cataloging\Entity\CatalogCategoryReviewAssignmentEntity;
use App\Cataloging\Event\CategoryChangeRequestAssigned;
use App\Cataloging\PolicyInterface\CatalogCategoryReviewAssignmentEntityPolicyInterface;
use App\Cataloging\RepositoryInterface\CatalogCategoryReviewAssignmentEntityRepositoryInterface;
use App\Cataloging\RepositoryInterface\CategoryChangeRequestRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogReviewAssignmentServiceInterface;
use App\Cataloging\ValueObject\CatalogCategoryReviewAssignmentEntityRequest;

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
        private CatalogCategoryReviewAssignmentEntityRepositoryInterface $assignmentRepository,
        private CatalogCategoryReviewAssignmentEntityPolicyInterface $policy,
    ) {
    }

    /**
     * Handles the assign workflow.
     */
    public function assign(CatalogCategoryReviewAssignmentEntityRequest $request): CategoryChangeRequestAssigned
    {
        $changeRequest = $this->changeRequestRepository->findByRequestId($request->requestId());

        if (!$changeRequest instanceof CatalogCategoryChangeRequestEntity) {
            throw new \DomainException(sprintf('Category change request not found: %s', $request->requestId()));
        }

        $this->policy->assertCanAssign(
            $changeRequest,
            $request->assignedReviewer(),
            $request->assignedBy(),
            $request->priority(),
        );

        $assignment = CatalogCategoryReviewAssignmentEntity::create(
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
