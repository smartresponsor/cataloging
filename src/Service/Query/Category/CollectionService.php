<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Service\Query\Category;

use App\ServiceInterface\Query\Category\CollectionServiceInterface;

final class CollectionService implements CollectionServiceInterface
{
    public function filter(array $products, string $rule): array
    {
        $tokens = preg_split('/\s+/', trim($rule)) ?: [];
        $operator = 'AND';
        $predicates = [];

        foreach ($tokens as $token) {
            if ('AND' === $token || 'OR' === $token) {
                $operator = $token;
                continue;
            }

            if (str_starts_with($token, 'tag:')) {
                $tag = substr($token, 4);
                $predicates[] = static fn (array $product): bool => in_array($tag, $product['tags'] ?? [], true);
                continue;
            }

            if (str_starts_with($token, 'category:')) {
                $categoryId = substr($token, 9);
                $predicates[] = static fn (array $product): bool => in_array($categoryId, $product['categoryIds'] ?? [], true);
                continue;
            }

            if (1 === preg_match('/^price([<>]=?)(\d+(?:\.\d+)?)$/', $token, $match)) {
                $comparison = $match[1];
                $value = (float) $match[2];
                $predicates[] = static function (array $product) use ($comparison, $value): bool {
                    $price = (float) ($product['price'] ?? 0);

                    return match ($comparison) {
                        '>' => $price > $value,
                        '>=' => $price >= $value,
                        '<' => $price < $value,
                        '<=' => $price <= $value,
                        default => false,
                    };
                };
            }
        }

        $result = [];
        foreach ($products as $product) {
            $passed = 'AND' === $operator;
            foreach ($predicates as $predicate) {
                $matched = $predicate($product);
                if ('AND' === $operator) {
                    $passed = $passed && $matched;
                    if (!$passed) {
                        break;
                    }
                    continue;
                }

                $passed = $passed || $matched;
                if ($passed) {
                    break;
                }
            }

            if ($passed) {
                $result[] = $product;
            }
        }

        return $result;
    }
}
