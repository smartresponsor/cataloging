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
    public function compile(testsRule $rule): array
    {
        $where = [];
        $params = [];
        $i = 0;

        $spec = $rule->spec();
        $conditions = $spec['all'] ?? [];
        if (!is_array($conditions)) {
            return ['sql' => '1=1', 'params' => []];
        }

        foreach ($conditions as $cond) {
            if (!is_array($cond)) {
                continue;
            }

            if (isset($cond['attr'])) {
                $attr = (string) $cond['attr'];
                $op = isset($cond['op']) ? (string) $cond['op'] : '';
                $val = $cond['value'] ?? null;

                if ('in' === $op && is_array($val)) {
                    $marks = [];
                    foreach ($val as $v) {
                        $key = ':p'.$i++;
                        $marks[] = $key;
                        $params[$key] = $v;
                    }
                    if ([] !== $marks) {
                        $where[] = sprintf('%s IN (%s)', $attr, implode(',', $marks));
                    }
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
            } elseif (isset($cond['tag'])) {
                $a = ':p'.$i++;
                $where[] = 'JSON_CONTAINS(tag_set, '.$a.')';
                $params[$a] = json_encode($cond['tag'], JSON_THROW_ON_ERROR);
            }
        }

        $sql = [] !== $where ? '('.implode(' AND ', $where).')' : '1=1';

        return [
            'sql' => $sql,
            'params' => $params,
        ];
    }
}
