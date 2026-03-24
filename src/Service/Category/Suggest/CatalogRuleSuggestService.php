<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category\Suggest;

final class CatalogRuleSuggestService
{
    /**
     * Build a simple heuristic rule: pick most frequent brand and 80th percentile price threshold.
     *
     * @param list<array{price:float, brand?:string, categoryId?:string}> $sample
     *
     * @return array<string,mixed> rule
     */
    public function suggest(array $sample): array
    {
        if (0 === count($sample)) {
            return ['any' => [['attr' => 'price', 'op' => 'lte', 'value' => 100]]];
        }
        $brands = [];
        $prices = [];
        foreach ($sample as $p) {
            if (isset($p['brand']) && is_string($p['brand'])) {
                $brands[$p['brand']] = ($brands[$p['brand']] ?? 0) + 1;
            }
            if (isset($p['price']) && is_numeric($p['price'])) {
                $prices[] = (float) $p['price'];
            }
        }
        arsort($brands);
        sort($prices);
        $idx = (int) floor(0.8 * max(0, count($prices) - 1));
        $p80 = $prices[$idx] ?? 100.0;
        $topBrand = array_key_first($brands);
        if ($topBrand) {
            return ['all' => [
                ['attr' => 'brand', 'op' => 'eq', 'value' => $topBrand],
                ['attr' => 'price', 'op' => 'lte', 'value' => $p80],
            ]];
        }

        return ['any' => [['attr' => 'price', 'op' => 'lte', 'value' => $p80]]];
    }
}
