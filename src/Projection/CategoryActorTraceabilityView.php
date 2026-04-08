<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Projection;
/**
 * Provides the category actor traceability view implementation.
 */
final class CategoryActorTraceabilityView
{
    /**
     * @param list<array<string,mixed>>                          $accessAssignments
     * @param list<array<string,mixed>>                          $changeRequests
     * @param list<array<string,mixed>>                          $reviewAssignments
     * @param list<array<string,mixed>>                          $mediaBindings
     * @param list<array<string,mixed>>                          $workflowHistory
     * @param array<string,array{count:int, roles:list<string>}> $actorSummary
     */
    public function __construct(
        public readonly string $categoryId,
        public readonly array $accessAssignments,
        public readonly array $changeRequests,
        public readonly array $reviewAssignments,
        public readonly array $mediaBindings,
        public readonly array $workflowHistory,
        public readonly array $actorSummary,
        public readonly string $generatedAt,
    ) {
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'categoryId' => $this->categoryId,
            'accessAssignments' => $this->accessAssignments,
            'changeRequests' => $this->changeRequests,
            'reviewAssignments' => $this->reviewAssignments,
            'mediaBindings' => $this->mediaBindings,
            'workflowHistory' => $this->workflowHistory,
            'actorSummary' => $this->actorSummary,
            'generatedAt' => $this->generatedAt,
        ];
    }
}
