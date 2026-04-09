<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Projection;

/**
 * Provides the category runtime status view implementation.
 */
final readonly class CategoryRuntimeStatusView
{
    /**
     * @param array<string,mixed> $governance
     * @param array<string,mixed> $traceability
     * @param array<string,mixed> $workflow
     * @param array<string,mixed> $review
     * @param array<string,bool>  $surfaceStatus
     */
    public function __construct(
        public string $categoryId,
        public array $governance,
        public array $traceability,
        public array $workflow,
        public array $review,
        public array $surfaceStatus,
        public string $generatedAt,
    ) {
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'categoryId' => $this->categoryId,
            'governance' => $this->governance,
            'traceability' => $this->traceability,
            'workflow' => $this->workflow,
            'review' => $this->review,
            'surfaceStatus' => $this->surfaceStatus,
            'generatedAt' => $this->generatedAt,
        ];
    }
}
