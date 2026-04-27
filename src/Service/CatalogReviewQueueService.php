<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\EntityInterface\Catalog\CatalogCategoryChangeRequestEntityInterface;
use App\Cataloging\EntityInterface\Catalog\CatalogCategoryReviewAssignmentEntityInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryChangeRequestRepositoryInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryReviewAssignmentRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogReviewQueueServiceInterface;
use App\Cataloging\ValueObject\CategoryChangeRequestState;
use App\Cataloging\ValueObject\CategoryReviewQueueItem;
use App\Cataloging\ValueObject\CategoryReviewQueueRequest;

/**
 * Provides the catalog review queue service application service.
 */
final readonly class CatalogReviewQueueService implements CatalogReviewQueueServiceInterface
{
    /**
     * Initializes the catalog review queue service service collaborators.
     */
    public function __construct(
        private CatalogCategoryChangeRequestRepositoryInterface $changeRequestRepository,
        private CatalogCategoryReviewAssignmentRepositoryInterface $assignmentRepository,
    ) {
    }

    /**
     * Handles the queue for reviewer workflow.
     */
    public function queueForReviewer(CategoryReviewQueueRequest $request): array
    {
        $items = [];

        foreach ($this->assignmentRepository->findByReviewer($request->reviewer()) as $assignment) {
            $changeRequest = $this->changeRequestRepository->findByRequestId($assignment->requestId());

            if (!$changeRequest instanceof CatalogCategoryChangeRequestEntityInterface) {
                continue;
            }

            $items[] = $this->buildQueueItem($assignment, $changeRequest);
        }

        usort($items, static function (CategoryReviewQueueItem $left, CategoryReviewQueueItem $right): int {
            return self::priorityRank($left->priority()) <=> self::priorityRank($right->priority());
        });

        return $items;
    }

    private function buildQueueItem(
        CatalogCategoryReviewAssignmentEntityInterface $assignment,
        CatalogCategoryChangeRequestEntityInterface $changeRequest,
    ): CategoryReviewQueueItem {
        $warnings = [];
        $state = $changeRequest->state()->value();
        $readyForReview = true;

        if (CategoryChangeRequestState::PROPOSED === $state) {
            $readyForReview = false;
            $warnings[] = 'request_not_started';
        }

        if ([] === $changeRequest->changes()) {
            $readyForReview = false;
            $warnings[] = 'request_changes_missing';
        }

        if ('' === trim($changeRequest->summary())) {
            $readyForReview = false;
            $warnings[] = 'request_summary_missing';
        }

        return CategoryReviewQueueItem::create(
            $changeRequest->requestId(),
            $changeRequest->categoryId(),
            $assignment->assignedReviewer(),
            $assignment->priority(),
            $state,
            $readyForReview,
            $warnings,
            $assignment->dueAt(),
        );
    }

    private static function priorityRank(string $priority): int
    {
        return match ($priority) {
            'urgent' => 0,
            'high' => 1,
            default => 2,
        };
    }
}
