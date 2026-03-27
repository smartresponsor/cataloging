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

final class CatalogReviewDecisionCouplingService implements CatalogReviewDecisionCouplingServiceInterface
{
    public function __construct(private readonly CatalogChangeRequestServiceInterface $changeRequestService, private readonly CatalogWorkflowTransitionServiceInterface $workflowTransitionService, private readonly CatalogPublicationGateServiceInterface $publicationGateService)
    {
    }

    public function couple(string $requestId, string $targetState, string $reviewedBy, string $decisionReason, array $checks = []): CategoryReviewDecisionCoupledInterface
    {
        $normalizedTargetState = trim($targetState);
        if (!in_array($normalizedTargetState, ['accepted', 'rejected'], true)) {
            throw new \DomainException(sprintf('Unsupported review decision coupling state: %s', $normalizedTargetState));
        }
        $reviewEvent = $this->changeRequestService->review($requestId, $normalizedTargetState, $reviewedBy, $decisionReason);
        $reviewPayload = $reviewEvent->payload();
        $categoryId = $this->scalarString($reviewPayload['categoryId'] ?? null);
        if ('' === $categoryId) {
            throw new \DomainException(sprintf('Review event for request %s does not contain categoryId.', $requestId));
        }
        if ('accepted' === $normalizedTargetState) {
            $workflowEvent = $this->workflowTransitionService->transition($categoryId, 'approved', $reviewedBy, sprintf('accepted change request %s', $requestId));
            $workflowPayload = $workflowEvent->payload();
            $gateEvent = $this->publicationGateService->evaluate($categoryId, 'approved', $checks, $reviewedBy, $decisionReason);
            $gatePayload = $gateEvent->payload();

            return new CategoryReviewDecisionCoupled($requestId, $categoryId, $normalizedTargetState, $this->scalarString($workflowPayload['toState'] ?? 'approved'), (bool) ($gatePayload['publishable'] ?? false), $this->stringList($gatePayload['blockers'] ?? null), $this->stringList($gatePayload['warnings'] ?? null), $this->boolMap($gatePayload['checks'] ?? null), trim($reviewedBy), trim($decisionReason), new \DateTimeImmutable('now'));
        }
        $workflowEvent = $this->workflowTransitionService->transition($categoryId, 'draft', $reviewedBy, sprintf('rejected change request %s', $requestId));
        $workflowPayload = $workflowEvent->payload();

        return new CategoryReviewDecisionCoupled($requestId, $categoryId, $normalizedTargetState, $this->scalarString($workflowPayload['toState'] ?? 'draft'), false, ['review_rejected'], [], $this->boolMap($checks), trim($reviewedBy), trim($decisionReason), new \DateTimeImmutable('now'));
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
        } $r = [];
        foreach ($value as $i) {
            if (!is_scalar($i)) {
                continue;
            } $n = trim((string) $i);
            if ('' !== $n) {
                $r[] = $n;
            }
        }

return array_values($r);
    }

    /** @return array<string,bool> */
    private function boolMap(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        } $r = [];
        foreach ($value as $k => $v) {
            if (is_string($k) && '' !== trim($k)) {
                $r[$k] = (bool) $v;
            }
        }

return $r;
    }
}
