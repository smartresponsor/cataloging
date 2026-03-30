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

final class CatalogDestinationMediaReadinessService implements CatalogDestinationMediaReadinessServiceInterface
{
    public function __construct(
        private readonly CategorySyndicationDestinationRepositoryInterface $destinationRepository,
        private readonly CatalogMediaApplicabilityServiceInterface $applicabilityService,
        private readonly CategoryDestinationMediaReadinessPolicyInterface $policy,
    ) {
    }

    public function evaluate(string $destinationId, string $categoryId, string $actorId, string $reason): CategoryDestinationMediaReadinessEvaluatedInterface
    {
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
        $applicability = $this->applicabilityService->evaluate($categoryId, $payload, $actorId, $reason);
        $applicabilityPayload = $applicability->payload();
        $report = $this->policy->buildReport(
            $destinationId,
            $categoryId,
            $settings,
            $payload,
            $this->boolMap($applicabilityPayload['checks'] ?? null),
            $this->stringList($applicabilityPayload['requiredMissing'] ?? null),
            $this->stringList($applicabilityPayload['warnings'] ?? null),
            $this->stringList($applicabilityPayload['matchedBindingIds'] ?? null),
        );

        return new CategoryDestinationMediaReadinessEvaluated(
            trim($destinationId), trim($categoryId), $this->stringValue($settings['channel'] ?? null), $this->stringValue($settings['locale'] ?? null),
            $report->publishable(), $report->requiredMissing(), $report->warnings(), $report->checks(), $report->matchedBindingIds(), trim($actorId), trim($reason), new \DateTimeImmutable()
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
            } $normalized = trim((string) $item);
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
        } $result = [];
        foreach ($value as $k => $v) {
            if (is_string($k) && '' !== trim($k)) {
                $result[$k] = (bool) $v;
            }
        }

        return $result;
    }
}
