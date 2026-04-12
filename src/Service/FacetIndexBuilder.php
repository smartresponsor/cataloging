<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the facet index builder application service.
 */
final class FacetIndexBuilder
{
    /**
     * @param array{id?:mixed,slug?:mixed,path?:mixed,locale?:mixed,name?:mixed} $category
     *
     * @return array{id:string,slug:string,path:string,locale:string,name:string}
     */
    public function build(array $category): array
    {
        return [
            'id' => $this->stringValue($category, 'id'),
            'slug' => $this->stringValue($category, 'slug'),
            'path' => $this->stringValue($category, 'path'),
            'locale' => $this->stringValue($category, 'locale', 'en'),
            'name' => $this->stringValue($category, 'name'),
        ];
    }

    /** @param array<string,mixed> $input */
    private function stringValue(array $input, string $key, string $default = ''): string
    {
        $value = $input[$key] ?? $default;

        return is_scalar($value) ? (string) $value : $default;
    }
}
