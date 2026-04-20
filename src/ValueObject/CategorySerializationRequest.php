<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the normalized category serialization filter surface.
 */
final readonly class CategorySerializationRequest
{
    /**
     * @param array<string,mixed> $source
     * @param list<string>        $includeFieldList
     * @param list<string>        $excludeFieldList
     */
    public function __construct(
        private array $source,
        private array $includeFieldList,
        private array $excludeFieldList,
    ) {
    }

    /** @return array<string,mixed> */
    public function source(): array
    {
        return $this->source;
    }

    /** @return list<string> */
    public function includeFieldList(): array
    {
        return $this->includeFieldList;
    }

    /** @return list<string> */
    public function excludeFieldList(): array
    {
        return $this->excludeFieldList;
    }
}
