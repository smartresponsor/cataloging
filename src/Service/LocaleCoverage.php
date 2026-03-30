<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class LocaleCoverage
{
    /**
     * @param list<array{id?:mixed,locale?:mixed}> $categories
     *
     * @return list<array{id:string,has_en:bool,has_uk:bool,has_es:bool}>
     */
    public function build(array $categories): array
    {
        /** @var array<string,array<string,bool>> $matrix */
        $matrix = [];
        foreach ($categories as $cat) {
            $loc = $this->stringValue($cat, 'locale', 'en');
            $id = $this->stringValue($cat, 'id', 'unknown');
            $matrix[$id][$loc] = true;
        }
        $out = [];
        foreach ($matrix as $id => $locales) {
            $out[] = [
                'id' => $id,
                'has_en' => isset($locales['en']),
                'has_uk' => isset($locales['uk']),
                'has_es' => isset($locales['es']),
            ];
        }
        file_put_contents('report/category-locale-matrix.json', json_encode($out, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return $out;
    }

    /** @param array<string,mixed> $row */
    private function stringValue(array $row, string $key, string $default): string
    {
        $value = $row[$key] ?? $default;

        return is_scalar($value) ? (string) $value : $default;
    }
}
