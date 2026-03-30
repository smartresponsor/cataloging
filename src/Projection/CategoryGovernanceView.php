<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Projection;

final class CategoryGovernanceView
{
    /**
     * @param list<array<string,mixed>> $activeAssignments
     * @param array<string,bool>        $roleCapabilities
     */
    public function __construct(
        public readonly string $categoryId,
        public readonly ?string $primaryActorUserId,
        public readonly array $activeAssignments,
        public readonly array $roleCapabilities,
        public readonly string $generatedAt,
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
