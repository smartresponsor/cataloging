<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategoryDestinationMediaReadinessEvaluated;
use App\EventInterface\CategoryDestinationMediaReadinessEvaluatedInterface;
use App\PolicyInterface\CategoryDestinationMediaReadinessPolicyInterface;
use App\RepositoryInterface\CategorySyndicationDestinationRepositoryInterface;
use App\ServiceInterface\CatalogDestinationMediaReadinessServiceInterface;
use App\ServiceInterface\CatalogMediaApplicabilityServiceInterface;
use App\ValueObject\CategoryDestinationMediaEvaluationRequest;
use App\ValueObject\CategoryDestinationMediaReadinessContext;
use App\ValueObject\CategoryDestinationMediaReadinessState;
use App\ValueObject\CategoryEvaluationRequest;

/**
 * Provides the catalog destination media readiness service application service.
 */
final readonly class CatalogDestinationMediaReadinessService implements CatalogDestinationMediaReadinessServiceInterface
{
    /**
     * Initializes the catalog destination media readiness service service collaborators.
     */
    public function __construct(
        private CategorySyndicationDestinationRepositoryInterface $destinationRepository,
        private CatalogMediaApplicabilityServiceInterface $applicabilityService,
        private CategoryDestinationMediaReadinessPolicyInterface $policy,
    ) {
    }

    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(
        CategoryDestinationMediaEvaluationRequest $request,
    ): CategoryDestinationMediaReadinessEvaluatedInterface {
        $destinationId = $request->destinationId();
        $categoryId = $request->categoryId();
        $destination = $this->destinationRepository->find($destinationId);
        if (null === $destination) {
            throw new \InvalidArgumentException('Unknown destination.');
        }
        $settings = $destination->settings();
        $payload = [
            'channel' => $this->stringValue($settings['channel'] ?? null),
            'locale' => $this->stringValue($settings['locale'] ?? null),
            'requiredRoles' => $this->stringList($settings['requiredMediaRoles'] ?? null),
        ];
        $applicability = $this->applicabilityService->evaluate(
            new CategoryEvaluationRequest(
                $categoryId,
                $payload,
                $request->auditContext(),
            ),
        );
        $applicabilityPayload = $applicability->payload();

        $report = $this->policy->buildReport(
            new CategoryDestinationMediaReadinessContext(
                $destinationId,
                $categoryId,
                $settings,
                $payload,
            ),
            new CategoryDestinationMediaReadinessState(
                $this->boolMap($applicabilityPayload['checks'] ?? null),
                $this->stringList($applicabilityPayload['requiredMissing'] ?? null),
                $this->stringList($applicabilityPayload['warnings'] ?? null),
                $this->stringList($applicabilityPayload['matchedBindingIds'] ?? null),
            ),
        );

        return new CategoryDestinationMediaReadinessEvaluated(
            $destinationId,
            $categoryId,
            $this->stringValue($settings['channel'] ?? null),
            $this->stringValue($settings['locale'] ?? null),
            $report->publishable(),
            $report->requiredMissing(),
            $report->warnings(),
            $report->checks(),
            $report->matchedBindingIds(),
            $request->actorId(),
            $request->reason(),
            new \DateTimeImmutable(),
        );
    }

    private function stringValue(mixed $value): string
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
        foreach ($value as $key => $item) {
            if (is_string($key) && '' != trim($key)) {
                $result[$key] = (bool) $item;
            }
        }

        return $result;
    }
}
