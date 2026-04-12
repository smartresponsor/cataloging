<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Traceability;

use App\Projection\CategoryActorTraceabilityView;
use App\RepositoryInterface\CategoryAccessAssignmentRepositoryInterface;
use App\RepositoryInterface\CategoryChangeRequestRepositoryInterface;
use App\RepositoryInterface\CategoryMediaBindingRepositoryInterface;
use App\RepositoryInterface\CategoryReviewAssignmentRepositoryInterface;
use App\RepositoryInterface\CategoryWorkflowRepositoryInterface;
use App\ServiceInterface\Traceability\CategoryActorTraceabilityViewBuilderInterface;

/**
 * Provides the category actor traceability view builder application service.
 */
final readonly class CategoryActorTraceabilityViewBuilder implements CategoryActorTraceabilityViewBuilderInterface
{
    /**
     * Initializes the category actor traceability view builder service collaborators.
     */
    public function __construct(
        private CategoryAccessAssignmentRepositoryInterface $accessAssignmentRepository,
        private CategoryChangeRequestRepositoryInterface $changeRequestRepository,
        private CategoryReviewAssignmentRepositoryInterface $reviewAssignmentRepository,
        private CategoryMediaBindingRepositoryInterface $mediaBindingRepository,
        private CategoryWorkflowRepositoryInterface $workflowRepository,
    ) {
    }

    /**
     * Builds the requested output for the current workflow.
     */
    public function build(string $categoryId): CategoryActorTraceabilityView
    {
        $categoryId = trim($categoryId);
        $accessAssignments = [];
        $changeRequests = [];
        $reviewAssignments = [];
        $mediaBindings = [];
        $workflowHistory = [];
        /** @var array<string,array{count:int, roles:list<string>}> $actorSummary */
        $actorSummary = [];

        foreach ($this->accessAssignmentRepository->findActiveByCategoryId($categoryId) as $assignment) {
            $row = [
                'assignmentId' => $assignment->assignmentId(),
                'actorUserId' => $assignment->actorUserId(),
                'role' => $assignment->role(),
                'status' => $assignment->status(),
                'isPrimary' => $assignment->isPrimary(),
                'grantedAt' => $assignment->grantedAt()->format(DATE_ATOM),
                'revokedAt' => $assignment->revokedAt()?->format(DATE_ATOM),
            ];
            $accessAssignments[] = $row;
            $this->touchActor($actorSummary, $assignment->actorUserId(), 'access:'.$assignment->role());
        }

        foreach ($this->changeRequestRepository->findByCategoryId($categoryId) as $request) {
            $row = [
                'requestId' => $request->requestId(),
                'submittedBy' => $request->submittedBy(),
                'reviewedBy' => $request->reviewedBy(),
                'state' => $request->state()->value(),
                'submittedAt' => $request->submittedAt()->format(DATE_ATOM),
                'reviewedAt' => $request->reviewedAt()?->format(DATE_ATOM),
            ];
            $changeRequests[] = $row;
            $this->touchActor($actorSummary, $request->submittedBy(), 'change-request:submitted');
            if (null !== $request->reviewedBy()) {
                $this->touchActor($actorSummary, $request->reviewedBy(), 'change-request:reviewed');
            }
        }

        foreach ($this->reviewAssignmentRepository->findByCategoryId($categoryId) as $assignment) {
            $row = [
                'requestId' => $assignment->requestId(),
                'assignedReviewer' => $assignment->assignedReviewer(),
                'assignedBy' => $assignment->assignedBy(),
                'priority' => $assignment->priority(),
                'assignedAt' => $assignment->assignedAt()->format(DATE_ATOM),
                'dueAt' => $assignment->dueAt()?->format(DATE_ATOM),
            ];
            $reviewAssignments[] = $row;
            $this->touchActor($actorSummary, $assignment->assignedReviewer(), 'review:assignee');
            $this->touchActor($actorSummary, $assignment->assignedBy(), 'review:assigner');
        }

        foreach ($this->mediaBindingRepository->bindingsForCategory($categoryId) as $binding) {
            $row = [
                'bindingId' => $binding->bindingId(),
                'assetId' => $binding->assetId(),
                'role' => $binding->role()->value(),
                'actorId' => $binding->actorId(),
                'boundAt' => $binding->boundAt()->format(DATE_ATOM),
            ];
            $mediaBindings[] = $row;
            $this->touchActor($actorSummary, $binding->actorId(), 'media:bound');
        }

        foreach ($this->workflowRepository->historyForCategoryId($categoryId) as $event) {
            $payload = $event->payload();
            $actorId = $this->actorIdFromPayload($payload);
            $row = [
                'eventName' => $event->eventName(),
                'actorId' => $actorId,
                'fromState' => $payload['fromState'] ?? null,
                'toState' => $payload['toState'] ?? null,
                'reason' => $payload['reason'] ?? null,
                'occurredAt' => $payload['occurredAt'] ?? null,
            ];
            $workflowHistory[] = $row;
            if ('' !== $actorId) {
                $this->touchActor($actorSummary, $actorId, 'workflow:transition');
            }
        }

        ksort($actorSummary);
        foreach ($actorSummary as &$summary) {
            $summary['roles'] = array_values(array_unique($summary['roles']));
            sort($summary['roles']);
        }

        $generatedAtDateTime = new \DateTimeImmutable('now');

        return new CategoryActorTraceabilityView(
            categoryId: $categoryId,
            accessAssignments: $accessAssignments,
            changeRequests: $changeRequests,
            reviewAssignments: $reviewAssignments,
            mediaBindings: $mediaBindings,
            workflowHistory: $workflowHistory,
            actorSummary: $actorSummary,
            generatedAt: $generatedAtDateTime->format(DATE_ATOM),
        );
    }

    /** @param array<string,mixed> $payload */
    private function actorIdFromPayload(array $payload): string
    {
        $value = $payload['actorId'] ?? null;

        return is_scalar($value) ? trim((string) $value) : '';
    }

    /** @param array<string,array{count:int, roles:list<string>}> $actorSummary */
    private function touchActor(array &$actorSummary, string $actorId, string $role): void
    {
        $actorId = trim($actorId);
        if ('' === $actorId) {
            return;
        }

        if (!isset($actorSummary[$actorId])) {
            $actorSummary[$actorId] = ['count' => 0, 'roles' => []];
        }

        ++$actorSummary[$actorId]['count'];
        $actorSummary[$actorId]['roles'][] = $role;
    }
}
