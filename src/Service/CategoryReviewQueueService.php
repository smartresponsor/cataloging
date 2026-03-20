<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

use App\EntityInterface\CategoryChangeRequestInterface;
use App\EntityInterface\CategoryReviewAssignmentInterface;
use App\RepositoryInterface\CategoryChangeRequestRepositoryInterface;
use App\RepositoryInterface\CategoryReviewAssignmentRepositoryInterface;
use App\ServiceInterface\CategoryReviewQueueServiceInterface;
use App\ValueObject\CategoryChangeRequestState;
use App\ValueObject\CategoryReviewQueueItem;

final class CategoryReviewQueueService implements CategoryReviewQueueServiceInterface
{
    public function __construct(
        private readonly CategoryChangeRequestRepositoryInterface $changeRequestRepository,
        private readonly CategoryReviewAssignmentRepositoryInterface $assignmentRepository,
    ) {
    }

    public function queueForReviewer(string $reviewer): array
    {
        $items = [];

        foreach ($this->assignmentRepository->findByReviewer($reviewer) as $assignment) {
            $request = $this->changeRequestRepository->findByRequestId($assignment->requestId());

            if (!$request instanceof CategoryChangeRequestInterface) {
                continue;
            }

            $items[] = $this->buildQueueItem($assignment, $request);
        }

        usort($items, static function (CategoryReviewQueueItem $left, CategoryReviewQueueItem $right): int {
            return self::priorityRank($left->priority()) <=> self::priorityRank($right->priority());
        });

        return $items;
    }

    private function buildQueueItem(
        CategoryReviewAssignmentInterface $assignment,
        CategoryChangeRequestInterface $request,
    ): CategoryReviewQueueItem {
        $warnings = [];
        $state = $request->state()->value();
        $readyForReview = true;

        if (CategoryChangeRequestState::PROPOSED === $state) {
            $readyForReview = false;
            $warnings[] = 'request_not_started';
        }

        if ([] === $request->changes()) {
            $readyForReview = false;
            $warnings[] = 'request_changes_missing';
        }

        if ('' === trim($request->summary())) {
            $readyForReview = false;
            $warnings[] = 'request_summary_missing';
        }

        return CategoryReviewQueueItem::create(
            $request->requestId(),
            $request->categoryId(),
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
