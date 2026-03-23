<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service\Ops;

use App\Projection\CategoryRuntimeStatusView;
use App\RepositoryInterface\CategoryReviewAssignmentRepositoryInterface;
use App\RepositoryInterface\CategoryWorkflowRepositoryInterface;
use App\Service\Governance\CategoryGovernanceViewBuilder;
use App\Service\Traceability\CategoryActorTraceabilityViewBuilder;
use App\ServiceInterface\Ops\CategoryRuntimeStatusViewBuilderInterface;

final class CategoryRuntimeStatusViewBuilder implements CategoryRuntimeStatusViewBuilderInterface
{
    public function __construct(
        private readonly CategoryGovernanceViewBuilder $governanceViewBuilder,
        private readonly CategoryActorTraceabilityViewBuilder $traceabilityViewBuilder,
        private readonly CategoryWorkflowRepositoryInterface $workflowRepository,
        private readonly CategoryReviewAssignmentRepositoryInterface $reviewAssignmentRepository,
    ) {
    }

    public function build(string $categoryId): CategoryRuntimeStatusView
    {
        $categoryId = trim($categoryId);
        $governance = $this->governanceViewBuilder->build($categoryId)->toArray();
        $traceability = $this->traceabilityViewBuilder->build($categoryId)->toArray();
        $workflow = $this->buildWorkflowSummary($categoryId);
        $review = $this->buildReviewSummary($categoryId);

        return new CategoryRuntimeStatusView(
            categoryId: $categoryId,
            governance: $governance,
            traceability: $traceability,
            workflow: $workflow,
            review: $review,
            surfaceStatus: [
                'governanceReady' => [] !== $governance['activeAssignments'] || null !== $governance['primaryActorUserId'],
                'traceabilityReady' => [] !== $traceability['actorSummary'],
                'workflowReady' => null !== $workflow['currentState'] || $workflow['historyCount'] > 0,
                'reviewReady' => $review['assignmentCount'] > 0,
            ],
            generatedAt: (new \DateTimeImmutable('now'))->format(DATE_ATOM),
        );
    }

    /** @return array<string,mixed> */
    private function buildWorkflowSummary(string $categoryId): array
    {
        $current = $this->workflowRepository->findByCategoryId($categoryId);
        $history = $this->workflowRepository->historyForCategoryId($categoryId);
        $latest = null;

        if ([] !== $history) {
            $event = $history[array_key_last($history)];
            $payload = $event->payload();
            $latest = [
                'eventName' => $event->eventName(),
                'fromState' => $payload['fromState'] ?? null,
                'toState' => $payload['toState'] ?? null,
                'actorId' => $payload['actorId'] ?? null,
                'occurredAt' => $payload['occurredAt'] ?? null,
            ];
        }

        return [
            'currentState' => $current?->state()->value(),
            'historyCount' => count($history),
            'latestTransition' => $latest,
        ];
    }

    /** @return array<string,mixed> */
    private function buildReviewSummary(string $categoryId): array
    {
        $assignments = $this->reviewAssignmentRepository->findByCategoryId($categoryId);
        $rows = [];
        foreach ($assignments as $assignment) {
            $rows[] = [
                'requestId' => $assignment->requestId(),
                'assignedReviewer' => $assignment->assignedReviewer(),
                'assignedBy' => $assignment->assignedBy(),
                'priority' => $assignment->priority(),
                'assignedAt' => $assignment->assignedAt()->format(DATE_ATOM),
                'dueAt' => $assignment->dueAt()?->format(DATE_ATOM),
            ];
        }

        return [
            'assignmentCount' => count($rows),
            'assignments' => $rows,
        ];
    }
}
