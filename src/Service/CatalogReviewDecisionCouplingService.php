<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategoryReviewDecisionCoupled;
use App\EventInterface\CategoryReviewDecisionCoupledInterface;
use App\ServiceInterface\CatalogChangeRequestServiceInterface;
use App\ServiceInterface\CatalogPublicationGateServiceInterface;
use App\ServiceInterface\CatalogReviewDecisionCouplingServiceInterface;
use App\ServiceInterface\CatalogWorkflowTransitionServiceInterface;
use App\ValueObject\CategoryChangeRequestReviewRequest;
use App\ValueObject\CategoryPublicationGateEvaluationRequest;
use App\ValueObject\CategoryReviewDecisionCouplingRequest;
use App\ValueObject\CategoryWorkflowTransitionRequest;

/**
 * Provides the catalog review decision coupling service application service.
 */
final class CatalogReviewDecisionCouplingService implements CatalogReviewDecisionCouplingServiceInterface
{
    /**
     * Initializes the catalog review decision coupling service service collaborators.
     */
    public function __construct(
        private readonly CatalogChangeRequestServiceInterface $changeRequestService,
        private readonly CatalogWorkflowTransitionServiceInterface $workflowTransitionService,
        private readonly CatalogPublicationGateServiceInterface $publicationGateService,
    ) {
    }

    /**
     * Handles the couple workflow.
     */
    public function couple(CategoryReviewDecisionCouplingRequest $request): CategoryReviewDecisionCoupledInterface
    {
        $normalizedTargetState = trim($request->targetState());
        if (!in_array($normalizedTargetState, ['accepted', 'rejected'], true)) {
            throw new \DomainException(sprintf('Unsupported review decision coupling state: %s', $normalizedTargetState));
        }
        $reviewEvent = $this->changeRequestService->review(new CategoryChangeRequestReviewRequest(
            $request->requestId(),
            $normalizedTargetState,
            $request->reviewedBy(),
            $request->decisionReason(),
        ));
        $reviewPayload = $reviewEvent->payload();
        $categoryId = $this->scalarString($reviewPayload['categoryId'] ?? null);
        if ('' === $categoryId) {
            throw new \DomainException(sprintf('Review event for request %s does not contain categoryId.', $request->requestId()));
        }
        if ('accepted' === $normalizedTargetState) {
            $workflowEvent = $this->workflowTransitionService->transition(new CategoryWorkflowTransitionRequest(
                $categoryId,
                'approved',
                $request->reviewedBy(),
                sprintf('accepted change request %s', $request->requestId()),
            ));
            $workflowPayload = $workflowEvent->payload();
            $gateEvent = $this->publicationGateService->evaluate(new CategoryPublicationGateEvaluationRequest(
                $categoryId,
                'approved',
                $request->checks(),
                $request->reviewedBy(),
                $request->decisionReason(),
            ));
            $gatePayload = $gateEvent->payload();

            return new CategoryReviewDecisionCoupled(
                $request->requestId(),
                $categoryId,
                $normalizedTargetState,
                $this->scalarString($workflowPayload['toState'] ?? 'approved'),
                (bool) ($gatePayload['publishable'] ?? false),
                $this->stringList($gatePayload['blockers'] ?? null),
                $this->stringList($gatePayload['warnings'] ?? null),
                $this->boolMap($gatePayload['checks'] ?? null),
                trim($request->reviewedBy()),
                trim($request->decisionReason()),
                new \DateTimeImmutable('now'),
            );
        }
        $workflowEvent = $this->workflowTransitionService->transition(new CategoryWorkflowTransitionRequest(
            $categoryId,
            'draft',
            $request->reviewedBy(),
            sprintf('rejected change request %s', $request->requestId()),
        ));
        $workflowPayload = $workflowEvent->payload();

        return new CategoryReviewDecisionCoupled(
            $request->requestId(),
            $categoryId,
            $normalizedTargetState,
            $this->scalarString($workflowPayload['toState'] ?? 'draft'),
            false,
            ['review_rejected'],
            [],
            $this->boolMap($request->checks()),
            trim($request->reviewedBy()),
            trim($request->decisionReason()),
            new \DateTimeImmutable('now'),
        );
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    /** @return list<string> */
    private function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $result = [];
        foreach ($value as $item) {
            if (!is_scalar($item)) {
                continue;
            }

            $normalized = trim((string) $item);
            if ('' !== $normalized) {
                $result[] = $normalized;
            }
        }

        return array_values($result);
    }

    /** @return array<string,bool> */
    private function boolMap(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $result = [];
        foreach ($value as $checkName => $checkValue) {
            if (is_string($checkName) && '' !== trim($checkName)) {
                $result[$checkName] = (bool) $checkValue;
            }
        }

        return $result;
    }
}
