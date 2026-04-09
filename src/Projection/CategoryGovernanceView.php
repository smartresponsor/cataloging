<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Projection;

/**
 * Provides the category governance view implementation.
 */
final readonly class CategoryGovernanceView
{
    /**
     * @param list<array<string,mixed>> $activeAssignments
     * @param array<string,bool>        $roleCapabilities
     */
    public function __construct(
        public string $categoryId,
        public ?string $primaryActorUserId,
        public array $activeAssignments,
        public array $roleCapabilities,
        public string $generatedAt,
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'categoryId' => $this->categoryId,
            'primaryActorUserId' => $this->primaryActorUserId,
            'activeAssignments' => $this->activeAssignments,
            'roleCapabilities' => $this->roleCapabilities,
            'generatedAt' => $this->generatedAt,
        ];
    }
}
