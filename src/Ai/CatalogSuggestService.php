<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Ai;

/**
 * Provides the catalog suggest service implementation.
 */
final class CatalogSuggestService
{
    /** @var array<string,string[]> */
    private array $dict = [
        'electronics' => ['phone', 'laptop', 'camera', 'tv', 'audio', 'smart', 'headphone'],
        'apparel' => ['shirt', 'jeans', 'dress', 'jacket', 'shoe', 'sneaker', 'wear'],
        'home' => ['sofa', 'kitchen', 'cook', 'pan', 'furniture', 'bed', 'bath'],
        'beauty' => ['cosmetic', 'makeup', 'cream', 'skin', 'fragrance', 'perfume', 'hair'],
        'sport' => ['ball', 'fitness', 'yoga', 'bike', 'gym', 'run', 'swim'],
        'toy' => ['toy', 'lego', 'puzzle', 'kid', 'child', 'game'],
        'auto' => ['tire', 'oil', 'engine', 'car', 'truck', 'auto', 'battery'],
    ];

    /**
     * Suggest top-k categories with simple token match and explain.
     *
     * @param list<string> $tags
     *
     * @return list<array{category:string,score:int,explain:list<string>}>
     */
    public function suggest(string $name, string $desc = '', array $tags = [], int $k = 5): array
    {
        $tokens = $this->tokenize($name.' '.$desc.' '.implode(' ', $tags));
        /** @var array<string,array{score:int,explain:list<string>}> $scores */
        $scores = [];

        foreach ($this->dict as $category => $keywords) {
            $hits = 0;
            $explain = [];
            foreach ($keywords as $keyword) {
                if (!isset($tokens[$keyword])) {
                    continue;
                }

                ++$hits;
                $explain[] = $keyword;
            }

            if ($hits > 0) {
                $scores[$category] = ['score' => $hits, 'explain' => $explain];
            }
        }

        uasort(
            $scores,
            static fn (array $left, array $right): int => $right['score'] <=> $left['score'],
        );

        $result = [];
        foreach (array_slice($scores, 0, $k, true) as $category => $data) {
            $result[] = [
                'category' => $category,
                'score' => $data['score'],
                'explain' => $data['explain'],
            ];
        }

        return $result;
    }

    /** @return array<string,int> */
    private function tokenize(string $value): array
    {
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9 ]+/', ' ', $value) ?? $value;
        $result = [];
        foreach (explode(' ', $value) as $token) {
            $token = trim($token);
            if ('' === $token) {
                continue;
            }
            $result[$token] = ($result[$token] ?? 0) + 1;
        }

        return $result;
    }
}
