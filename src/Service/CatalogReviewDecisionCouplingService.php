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
/** @noinspection DuplicatedCode */
final readonly class CatalogReviewDecisionCouplingService implements CatalogReviewDecisionCouplingServiceInterface
{
    /**
     * Initializes the catalog review decision coupling service service collaborators.
     */
    public function __construct(
        private CatalogChangeRequestServiceInterface $changeRequestService,
        private CatalogWorkflowTransitionServiceInterface $workflowTransitionService,
        private CatalogPublicationGateServiceInterface $publicationGateService,
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
        $categoryId = CategoryPayloadValueNormalizer::scalarString($reviewPayload['categoryId'] ?? null);
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
                CategoryPayloadValueNormalizer::scalarString($workflowPayload['toState'] ?? 'approved'),
                (bool) ($gatePayload['publishable'] ?? false),
                CategoryPayloadValueNormalizer::stringList($gatePayload['blockers'] ?? null),
                CategoryPayloadValueNormalizer::stringList($gatePayload['warnings'] ?? null),
                CategoryPayloadValueNormalizer::boolMap($gatePayload['checks'] ?? null),
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
            CategoryPayloadValueNormalizer::scalarString($workflowPayload['toState'] ?? 'draft'),
            false,
            ['review_rejected'],
            [],
            CategoryPayloadValueNormalizer::boolMap($request->checks()),
            trim($request->reviewedBy()),
            trim($request->decisionReason()),
            new \DateTimeImmutable('now'),
        );
    }
}
