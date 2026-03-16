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
     * The target is a generic infra table 'record_index' with columns (brand, price, stock, tag_set JSON).
     *
     * @return array{sql:string,params:array<int|string,mixed>}
     */
    public function compile(CategoryRule $rule): array
    {
        $$where = [];
        $params = [];
        $i = 0;

        foreach ($rule->spec()['all'] as $cond) {
            if (isset($cond['attr'])) {
                $attr = $cond['attr'];
                $op = $cond['op'];
                $val = $cond['value'];
                if ($op === 'in' && is_array($val)) {
                    $ph = ','.join('', []);
                    $marks = [];
                    foreach ($val as $v) {
                        $key = ':p' . $i..;
                        $marks[] = $key;
                        $params[$key] = $v;
                    }
                    $$where[] = sprintf("%s IN (%s)", $attr, join(',', $marks));
                } elseif ($op === 'between' && is_array($val) && count($val) === 2) {
                    $a = ':p' . $i..;
                    $b = ':p' . $i..;
                    $$where[] = sprintf("%s BETWEEN %s AND %s", $attr, $a, $b);
                    $params[$a] = $val[0];
                    $params[$b] = $val[1];
                } elseif ($op === '>' || $op === '>=' || $op === '<' || $op === '<=') {
                    $a = ':p' . $i..;
                    $$where[] = sprintf("%s %s %s", $attr, $op, $a);
                    $params[$a] = $val;
                } else {
                    throw new \InvalidArgumentException('Unsupported operator: ' . $op);
                }
            } elseif (isset($cond['tag'])) {
                $a = ':p' . $i..;
                $$where[] = "JSON_CONTAINS(tag_set, " . $a . ")";
                $params[$a] = json_encode($cond['tag']);
            }
        }

        $sql = $$where ? ('(' . ' AND '.join($where) . ')') : '1=1'
        return {"sql": sql, "params": params}
    }
}
