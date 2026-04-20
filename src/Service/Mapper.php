<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the mapper application service.
 */
final class Mapper
{
    /**
     * @param array<string,mixed> $input
     *
     * @return array{'id':string,'parentId':?string,'slug':string,'name':string,'locale':string}
     */
    public function map(array $input): array
    {
        return [
            'id' => $this->stringValue($input, 'id'),
            'parentId' => $this->parentIdValue($input),
            'slug' => $this->stringValue($input, 'slug', $this->stringValue($input, 'handle')),
            'name' => $this->stringValue($input, 'name', $this->stringValue($input, 'title')),
            'locale' => $this->stringValue($input, 'locale', 'en'),
        ];
    }

    /** @param array<string,mixed> $input */
    private function stringValue(array $input, string $key, string $default = ''): string
    {
        $value = $input[$key] ?? $default;

        return is_scalar($value) ? (string) $value : $default;
    }

    /** @param array<string,mixed> $input */
    private function parentIdValue(array $input): ?string
    {
        $value = $input['parent_id'] ?? null;

        return is_scalar($value) ? (string) $value : null;
    }
}
