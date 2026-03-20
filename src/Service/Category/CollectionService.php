<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\Service\Category;

use App\ServiceInterface\Category\CollectionServiceInterface;

final class CollectionService implements CollectionServiceInterface
{
    public function filter(array $products, string $rule): array
    {
        $tokens = preg_split('/\s+/', trim($rule));
        $op = 'AND';
        $predicates = [];
        foreach ($tokens as $t) {
            if ('AND' === $t || 'OR' === $t) {
                $op = $t;
                continue;
            }
            if (str_starts_with($t, 'tag:')) {
                $tag = substr($t, 4);
                $predicates[] = fn ($p) => in_array($tag, $p['tags'] ?? [], true);
            } elseif (str_starts_with($t, 'category:')) {
                $cid = substr($t, 9);
                $predicates[] = fn ($p) => in_array($cid, $p['categoryIds'] ?? [], true);
            } elseif (preg_match('/^price([<>]=?)(\d+(?:\.\d+)?)$/', $t, $m)) {
                $opCmp = $m[1];
                $v = (float) $m[2];
                $predicates[] = function ($p) use ($opCmp, $v) {
                    $price = (float) ($p['price'] ?? 0);

                    return match ($opCmp) {
                        '>' => $price > $v,
                        '>=' => $price >= $v,
                        '<' => $price < $v,
                        '<=' => $price <= $v,
                        default => false,
                    };
                };
            }
        }
        $out = [];
        foreach ($products as $prod) {
            $res = ('AND' === $op);
            foreach ($predicates as $pred) {
                $ok = $pred($prod);
                if ('AND' === $op) {
                    $res = $res && $ok;
                    if (!$res) {
                        break;
                    }
                } else {
                    $res = $res || $ok;
                    if ($res) {
                        break;
                    }
                }
            }
            if ($res) {
                $out[] = $prod;
            }
        }

        return $out;
    }
}
