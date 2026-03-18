<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

use App\Entity\CategoryChangeRequest;
use App\Event\CategoryChangeRequestReviewed;
use App\PolicyInterface\CategoryChangeRequestPolicyInterface;
use App\RepositoryInterface\CategoryChangeRequestRepositoryInterface;
use App\ServiceInterface\CategoryChangeRequestServiceInterface;
use App\ValueObject\CategoryChangeRequestState;

final class CategoryChangeRequestService implements CategoryChangeRequestServiceInterface
{
    public function __construct(
        private readonly CategoryChangeRequestRepositoryInterface $repository,
        private readonly CategoryChangeRequestPolicyInterface $policy,
    ) {
    }

    public function submit(string $requestId, string $categoryId, string $submittedBy, string $summary, array $changes): CategoryChangeRequest
    {
        $this->policy->assertCanSubmit($requestId, $categoryId, $submittedBy, $summary, $changes);

        $request = CategoryChangeRequest::open($requestId, $categoryId, $submittedBy, $summary, $changes);
        $this->repository->save($request);

        return $request;
    }

    public function review(string $requestId, string $targetState, string $reviewedBy, string $decisionReason): CategoryChangeRequestReviewed
    {
        $request = $this->repository->findByRequestId($requestId);

        if (!$request instanceof CategoryChangeRequest) {
            throw new \DomainException(sprintf('Category change request not found: %s', $requestId));
        }

        $toState = CategoryChangeRequestState::fromString($targetState);
        $this->policy->assertCanReview($request->state(), $toState, $reviewedBy, $decisionReason);

        $updated = $request->moveTo($toState, $reviewedBy, $decisionReason);
        $this->repository->save($updated);

        $event = new CategoryChangeRequestReviewed(
            $updated->requestId(),
            $updated->categoryId(),
            $request->state()->value(),
            $toState->value(),
            $reviewedBy,
            $decisionReason,
            $updated->reviewedAt() ?? new \DateTimeImmutable('now'),
        );

        $this->repository->appendReviewHistory($event);

        return $event;
    }
}
