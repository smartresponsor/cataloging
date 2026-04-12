<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the catalog legacy collection filter service application service.
 */
final class CatalogLegacyCollectionFilterService
{
    /**
     * @param list<array<string,mixed>> $products
     *
     * @return list<array<string,mixed>>
     */
    public function filter(array $products, string $rule): array
    {
        $tokens = preg_split('/\s+/', trim($rule));
        if (false === $tokens) {
            $tokens = [];
        }
        $op = 'AND';
        /** @var list<callable(array<string,mixed>): bool> $predicates */
        $predicates = [];
        foreach ($tokens as $token) {
            if ('AND' === $token || 'OR' === $token) {
                $op = $token;
                continue;
            }
            if (str_starts_with($token, 'tag:')) {
                $tag = substr($token, 4);
                $predicates[] = static function (array $product) use ($tag): bool {
                    $tags = $product['tags'] ?? [];

                    return is_array($tags) && in_array($tag, $tags, true);
                };
                continue;
            }
            if (str_starts_with($token, 'category:')) {
                $categoryId = substr($token, 9);
                $predicates[] = static function (array $product) use ($categoryId): bool {
                    $categoryIds = $product['categoryIds'] ?? [];

                    return is_array($categoryIds) && in_array($categoryId, $categoryIds, true);
                };
                continue;
            }
            if (1 !== preg_match('/^price([<>]=?)(\d+(?:\.\d+)?)$/', $token, $matches)) {
                continue;
            }
            $operator = $matches[1];
            $expected = (float) $matches[2];
            $predicates[] = static function (array $product) use ($operator, $expected): bool {
                $priceValue = $product['price'] ?? 0;
                $price = is_numeric($priceValue) ? (float) $priceValue : 0.0;

                return match ($operator) {
                    '>' => $price > $expected,
                    '>=' => $price >= $expected,
                    '<' => $price < $expected,
                    '<=' => $price <= $expected,
                    default => false,
                };
            };
        }

        $out = [];
        foreach ($products as $product) {
            $result = ('AND' === $op);
            foreach ($predicates as $predicate) {
                $ok = $predicate($product);
                if ('AND' === $op) {
                    $result = $result && $ok;
                    if (!$result) {
                        break;
                    }
                    continue;
                }
                $result = $result || $ok;
                if ($result) {
                    break;
                }
            }
            if ($result) {
                $out[] = $product;
            }
        }

        return $out;
    }
}
