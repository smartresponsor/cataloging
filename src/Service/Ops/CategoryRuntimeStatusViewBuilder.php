<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service\Ops;

use App\Cataloging\Projection\CategoryRuntimeStatusView;
use App\Cataloging\RepositoryInterface\CategoryReviewAssignmentRepositoryInterface;
use App\Cataloging\RepositoryInterface\CategoryWorkflowRepositoryInterface;
use App\Cataloging\Service\Governance\CategoryGovernanceViewBuilder;
use App\Cataloging\Service\Traceability\CategoryActorTraceabilityViewBuilder;
use App\Cataloging\ServiceInterface\Ops\CategoryRuntimeStatusViewBuilderInterface;

/**
 * Provides the category runtime status view builder application service.
 */
final readonly class CategoryRuntimeStatusViewBuilder implements CategoryRuntimeStatusViewBuilderInterface
{
    /**
     * Initializes the category runtime status view builder service collaborators.
     */
    public function __construct(
        private CategoryGovernanceViewBuilder $governanceViewBuilder,
        private CategoryActorTraceabilityViewBuilder $traceabilityViewBuilder,
        private CategoryWorkflowRepositoryInterface $workflowRepository,
        private CategoryReviewAssignmentRepositoryInterface $reviewAssignmentRepository,
    ) {
    }

    /**
     * Builds the requested output for the current workflow.
     */
    public function build(string $categoryId): CategoryRuntimeStatusView
    {
        $categoryId = trim($categoryId);
        if ('' === $categoryId) {
            throw new \InvalidArgumentException('Category id must not be empty.');
        }

        $governance = $this->governanceViewBuilder->build($categoryId)->toArray();
        $traceability = $this->traceabilityViewBuilder->build($categoryId)->toArray();
        $workflow = $this->buildWorkflowSummary($categoryId);
        $review = $this->buildReviewSummary($categoryId);

        $generatedAtDateTime = new \DateTimeImmutable('now');

        return new CategoryRuntimeStatusView(
            categoryId: $categoryId,
            governance: $governance,
            traceability: $traceability,
            workflow: $workflow,
            review: $review,
            surfaceStatus: [
                'governanceReady' => [] !== $governance['activeAssignments']
                || null !== $governance['primaryActorUserId'],
                'traceabilityReady' => [] !== $traceability['actorSummary'],
                'workflowReady' => null !== $workflow['currentState'] || $workflow['historyCount'] > 0,
                'reviewReady' => $review['assignmentCount'] > 0,
            ],
            generatedAt: $generatedAtDateTime->format(DATE_ATOM),
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
