<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Service\Command\Category;

class RuleSuggestService
{
    public function suggest(array $sample): array
    {
        if ([] === $sample) {
            return ['any' => [['attr' => 'price', 'op' => 'lte', 'value' => 100]]];
        }

        $brands = [];
        $prices = [];
        foreach ($sample as $payload) {
            if (!is_array($payload)) {
                continue;
            }
            if (isset($payload['brand']) && is_string($payload['brand'])) {
                $brands[$payload['brand']] = ($brands[$payload['brand']] ?? 0) + 1;
            }
            if (isset($payload['price']) && is_numeric($payload['price'])) {
                $prices[] = (float) $payload['price'];
            }
        }

        arsort($brands);
        sort($prices);
        $index = (int) floor(0.8 * max(0, count($prices) - 1));
        $p80 = $prices[$index] ?? 100.0;
        $topBrand = array_key_first($brands);

        if (is_string($topBrand) && '' !== $topBrand) {
            return ['all' => [
                ['attr' => 'brand', 'op' => 'eq', 'value' => $topBrand],
                ['attr' => 'price', 'op' => 'lte', 'value' => $p80],
            ]];
        }

        return ['any' => [['attr' => 'price', 'op' => 'lte', 'value' => $p80]]];
    }
}
