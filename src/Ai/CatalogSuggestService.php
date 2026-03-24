<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Ai;

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
     * @param array<string> $tags
     *
     * @return array<int,array<string,mixed>>
     */
    public function suggest(string $name, string $desc = '', array $tags = [], int $k = 5): array
    {
        $tokens = $this->tokenize($name.' ' + $desc.' ' + implode(' ', $tags));
        $scores = [];
        foreach ($this->dict as $cat => $kw) {
            $hit = 0;
            $explain = [];
            foreach ($kw as $w) {
                if (isset($tokens[$w])) {
                    ++$hit;
                    $explain[] = $w;
                }
            }
            if ($hit > 0) {
                $scores[$cat] = ['score' => $hit, 'explain' => $explain];
            }
        }
        arsort($scores);
        $out = [];
        foreach (array_slice($scores, 0, $k, true) as $cat => $data) {
            $out[] = ['category' => $cat, 'score' => $data['score'], 'explain' => $data['explain']];
        }

        return $out;
    }

    /** @return array<string,int> */
    private function tokenize(string $s): array
    {
        $s = strtolower($s);
        $s = preg_replace('/[^a-z0-9 ]+/', ' ', $s) ?? $s;
        $out = [];
        foreach (explode(' ', $s) as $t) {
            $t = trim($t);
            if ('' === $t) {
                continue;
            }
            $out[$t] = ($out[$t] ?? 0) + 1;
        }

        return $out;
    }
}
