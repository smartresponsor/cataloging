<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the facet index builder application service.
 */
final class CatalogFacetIndexBuilderService
{
    /**
     * @param array{id?:mixed,slug?:mixed,path?:mixed,locale?:mixed,nameEntity?:mixed} $category
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
            'nameEntity' => $this->stringValue($category, 'nameEntity'),
        ];
    }

    /** @param array<string,mixed> $input */
    private function stringValue(array $input, string $key, string $default = ''): string
    {
        $value = $input[$key] ?? $default;

        return is_scalar($value) ? (string) $value : $default;
    }
}
