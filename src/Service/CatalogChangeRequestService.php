<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogCategoryChangeRequestEntity;
use App\Cataloging\Event\Catalog\CatalogCategoryChangeRequestReviewedEvent;
use App\Cataloging\PolicyInterface\CategoryChangeRequestPolicyInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryChangeRequestRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogChangeRequestServiceInterface;
use App\Cataloging\ValueObject\CategoryChangeRequestReviewRequest;
use App\Cataloging\ValueObject\CategoryChangeRequestState;
use App\Cataloging\ValueObject\CategoryChangeRequestSubmitRequest;

/**
 * Provides the catalog change request service application service.
 */
final readonly class CatalogChangeRequestService implements CatalogChangeRequestServiceInterface
{
    /**
     * Initializes the catalog change request service service collaborators.
     */
    public function __construct(
        private CatalogCategoryChangeRequestRepositoryInterface $repository,
        private CategoryChangeRequestPolicyInterface $policy,
    ) {
    }

    /**
     * Handles the submit workflow.
     */
    public function submit(CategoryChangeRequestSubmitRequest $request): CatalogCategoryChangeRequestEntity
    {
        $this->policy->assertCanSubmit(
            $request->requestId(),
            $request->categoryId(),
            $request->submittedBy(),
            $request->summary(),
            $request->changes(),
        );

        $entity = CatalogCategoryChangeRequestEntity::open(
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
    public function review(CategoryChangeRequestReviewRequest $request): CatalogCategoryChangeRequestReviewedEvent
    {
        $entity = $this->repository->findByRequestId($request->requestId());

        if (!$entity instanceof CatalogCategoryChangeRequestEntity) {
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

        $event = new CatalogCategoryChangeRequestReviewedEvent(
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
