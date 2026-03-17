<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Rule;

final class RuleEvaluator
{
    /**
     * Compile rule to SQL WHERE fragment and parameters.
     *
     * @return array{sql:string,params:array<string,mixed>}
     */
    public function compile(CategoryRule $rule): array
    {
        $where = [];
        $params = [];
        $i = 0;

        foreach ($rule->spec()['all'] as $cond) {
            if (isset($cond['attr'])) {
                $attr = (string) $cond['attr'];
                $op = (string) $cond['op'];
                $val = $cond['value'];

                if ('in' === $op && is_array($val)) {
                    $marks = [];
                    foreach ($val as $v) {
                        $key = ':p'.$i++;
                        $marks[] = $key;
                        $params[$key] = $v;
                    }
                    $where[] = sprintf('%s IN (%s)', $attr, implode(',', $marks));
                    continue;
                }

                if ('between' === $op && is_array($val) && 2 === count($val)) {
                    $a = ':p'.$i++;
                    $b = ':p'.$i++;
                    $where[] = sprintf('%s BETWEEN %s AND %s', $attr, $a, $b);
                    $params[$a] = $val[0];
                    $params[$b] = $val[1];
                    continue;
                }

                if (in_array($op, ['>', '>=', '<', '<='], true)) {
                    $key = ':p'.$i++;
                    $where[] = sprintf('%s %s %s', $attr, $op, $key);
                    $params[$key] = $val;
                    continue;
                }

                throw new \InvalidArgumentException('Unsupported operator: '.$op);
            }

            if (isset($cond['tag'])) {
                $key = ':p'.$i++;
                $where[] = 'JSON_CONTAINS(tag_set, '.$key.')';
                $params[$key] = json_encode($cond['tag'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        return [
            'sql' => [] !== $where ? '('.implode(' AND ', $where).')' : '1=1',
            'params' => $params,
        ];
    }
}
