<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

final class LocaleCoverage
{
    public function build(array $categories): array
    {
        $matrix = [];
        foreach ($categories as $cat) {
            $loc = $cat['locale'] ?? 'en';
            $id = $cat['id'] ?? 'unknown';
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
        file_put_contents('report/category-locale-matrix.json', json_encode($out, JSON_PRETTY_PRINT));

        return $out;
    }
}
