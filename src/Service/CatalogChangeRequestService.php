<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\CategoryChangeRequest;
use App\Event\CategoryChangeRequestReviewed;
use App\PolicyInterface\CategoryChangeRequestPolicyInterface;
use App\RepositoryInterface\CategoryChangeRequestRepositoryInterface;
use App\ServiceInterface\CatalogChangeRequestServiceInterface;
use App\ValueObject\CategoryChangeRequestReviewRequest;
use App\ValueObject\CategoryChangeRequestState;
use App\ValueObject\CategoryChangeRequestSubmitRequest;

/**
 * Provides the catalog change request service application service.
 */
final readonly class CatalogChangeRequestService implements CatalogChangeRequestServiceInterface
{
    /**
     * Initializes the catalog change request service service collaborators.
     */
    public function __construct(
        private CategoryChangeRequestRepositoryInterface $repository,
        private CategoryChangeRequestPolicyInterface $policy,
    ) {
    }

    /**
     * Handles the submit workflow.
     */
    public function submit(CategoryChangeRequestSubmitRequest $request): CategoryChangeRequest
    {
        $this->policy->assertCanSubmit(
            $request->requestId(),
            $request->categoryId(),
            $request->submittedBy(),
            $request->summary(),
            $request->changes(),
        );

        $entity = CategoryChangeRequest::open(
            $request->requestId(),
            $request->categoryId(),
            $request->submittedBy(),
            $request->summary(),
            $request->changes(),
        );
        $this->repository->save($entity);

        return $entity;
    }

    /**
     * Handles the review workflow.
     */
    public function review(CategoryChangeRequestReviewRequest $request): CategoryChangeRequestReviewed
    {
        $entity = $this->repository->findByRequestId($request->requestId());

        if (!$entity instanceof CategoryChangeRequest) {
            throw new \DomainException(sprintf('Category change request not found: %s', $request->requestId()));
        }

        $toState = CategoryChangeRequestState::fromString($request->targetState());
        $this->policy->assertCanReview(
            $entity->state(),
            $toState,
            $request->reviewedBy(),
            $request->decisionReason(),
        );

        $updated = $entity->moveTo($toState, $request->reviewedBy(), $request->decisionReason());
        $this->repository->save($updated);

        $event = new CategoryChangeRequestReviewed(
            $updated->requestId(),
            $updated->categoryId(),
            $entity->state()->value(),
            $toState->value(),
            $request->reviewedBy(),
            $request->decisionReason(),
            $updated->reviewedAt() ?? new \DateTimeImmutable('now'),
        );

        $this->repository->appendReviewHistory($event);

        return $event;
    }
}
