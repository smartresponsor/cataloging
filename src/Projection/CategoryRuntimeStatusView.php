<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Projection;

final class CategoryRuntimeStatusView
{
    /**
     * @param array<string,mixed> $governance
     * @param array<string,mixed> $traceability
     * @param array<string,mixed> $workflow
     * @param array<string,mixed> $review
     * @param array<string,bool>  $surfaceStatus
     */
    public function __construct(
        public readonly string $categoryId,
        public readonly array $governance,
        public readonly array $traceability,
        public readonly array $workflow,
        public readonly array $review,
        public readonly array $surfaceStatus,
        public readonly string $generatedAt,
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
