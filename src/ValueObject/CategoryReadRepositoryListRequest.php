<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries the normalized category read repository list options.
 */
final readonly class CategoryReadRepositoryListRequest
{
    public function __construct(
        private ?string $parentId = null,
        private ?string $search = null,
        private int $first = 20,
        private string $after = '',
        private bool $withTotal = false,
        private bool $approxTotal = false,
    ) {
    }

    /**
     * @param array{parentId?:mixed,search?:mixed,first?:mixed,after?:mixed,withTotal?:mixed,approxTotal?:mixed} $input
     */
    public static function fromArray(array $input): self
    {
        $parentId = isset($input['parentId']) && is_scalar($input['parentId']) ? trim((string) $input['parentId']) : null;
        $search = isset($input['search']) && is_scalar($input['search']) ? trim((string) $input['search']) : null;

        return new self(
            '' !== (string) $parentId ? $parentId : null,
            '' !== (string) $search ? $search : null,
            is_numeric($input['first'] ?? null) ? (int) $input['first'] : 20,
            is_scalar($input['after'] ?? null) ? (string) $input['after'] : '',
            (bool) ($input['withTotal'] ?? false),
            (bool) ($input['approxTotal'] ?? false),
        );
    }

    /** @return array{parentId?:string,search?:string,first:int,after:string} */
    public function criteria(): array
    {
        $criteria = [
            'first' => max(1, $this->first),
            'after' => trim($this->after),
        ];

        if (null !== $this->parentId) {
            $criteria['parentId'] = $this->parentId;
        }

        if (null !== $this->search) {
            $criteria['search'] = $this->search;
        }

        return $criteria;
    }

    public function withTotal(): bool
    {
        return $this->withTotal;
    }

    public function approxTotal(): bool
    {
        return $this->approxTotal;
    }
}
