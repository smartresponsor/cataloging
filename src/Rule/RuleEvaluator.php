<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Rule;

final class RuleEvaluator
{
    /** @return array{sql:string,params:array<string,mixed>} */
    public function compile(CategoryRule $rule): array
    {
        $where = [];
        $params = [];
        $i = 0;
        foreach ($rule->spec()['all'] as $cond) {
            $attrRaw = $cond['attr'] ?? null;
            if (is_scalar($attrRaw) && '' !== trim((string) $attrRaw)) {
                $attr = trim((string) $attrRaw);
                $opRaw = $cond['op'] ?? null;
                $op = is_scalar($opRaw) ? trim((string) $opRaw) : '';
                $val = $cond['value'] ?? null;
                if ('in' === $op && is_array($val)) {
                    $marks = [];
                    foreach ($val as $v) {
                        $key = ':p'.$i++;
                        $marks[] = $key;
                        $params[$key] = $v;
                    }
                    $where[] = sprintf('%s IN (%s)', $attr, implode(',', $marks));
                } elseif ('between' === $op && is_array($val) && 2 === count($val)) {
                    $a = ':p'.$i++;
                    $b = ':p'.$i++;
                    $where[] = sprintf('%s BETWEEN %s AND %s', $attr, $a, $b);
                    $params[$a] = $val[0];
                    $params[$b] = $val[1];
                } elseif (in_array($op, ['>', '>=', '<', '<='], true)) {
                    $a = ':p'.$i++;
                    $where[] = sprintf('%s %s %s', $attr, $op, $a);
                    $params[$a] = $val;
                } else {
                    throw new \InvalidArgumentException('Unsupported operator: '.$op);
                }
                continue;
            }
            $tagRaw = $cond['tag'] ?? null;
            if (is_scalar($tagRaw)) {
                $a = ':p'.$i++;
                $where[] = 'JSON_CONTAINS(tag_set, '.$a.')';
                $params[$a] = json_encode((string) $tagRaw, JSON_THROW_ON_ERROR);
            }
        }

        return ['sql' => [] !== $where ? '('.implode(' AND ', $where).')' : '1=1', 'params' => $params];
    }
}
