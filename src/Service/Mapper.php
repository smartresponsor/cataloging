<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

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
            'parentId' => $this->nullableStringValue($input, 'parent_id'),
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
    private function nullableStringValue(array $input, string $key): ?string
    {
        $value = $input[$key] ?? null;

        return is_scalar($value) ? (string) $value : null;
    }
}
