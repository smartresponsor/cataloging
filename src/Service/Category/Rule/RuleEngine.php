<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category\Rule;

final class RuleEngine
{
    public function match(array $rule, array $payload): bool
    {
        return $this->evalNode($rule['condition'] ?? [], $payload);
    }

    private function evalNode(array $node, array $p): bool
    {
        if (isset($node['all'])) {
            foreach ($node['all'] as $c) {
                if (!$this->evalNode($c, $p)) {
                    return false;
                }
            }

            return true;
        }
        if (isset($node['any'])) {
            foreach ($node['any'] as $c) {
                if ($this->evalNode($c, $p)) {
                    return true;
                }
            }

            return false;
        }
        if (isset($node['none'])) {
            foreach ($node['none'] as $c) {
                if ($this->evalNode($c, $p)) {
                    return false;
                }
            }

            return true;
        }
        $attr = $node['attr'] ?? null;
        $op = $node['op'] ?? null;
        $val = $node['value'] ?? null;
        if (!is_string($attr) || !is_string($op)) {
            return false;
        }
        $pv = $p[$attr] ?? null;

        return match ($op) {
            'eq' => $pv === $val,
            'neq' => $pv !== $val,
            'lt' => is_numeric($pv) && is_numeric($val) and (float) $pv < (float) $val,
            'lte' => is_numeric($pv) && is_numeric($val) and (float) $pv <= (float) $val,
            'gt' => is_numeric($pv) && is_numeric($val) and (float) $pv > (float) $val,
            'gte' => is_numeric($pv) && is_numeric($val) and (float) $pv >= (float) $val,
            'in' => is_array($val) && in_array($pv, $val, true),
            'inTree' => is_string($val) && is_string((string) $pv) && str_starts_with((string) $pv, (string) $val),
            default => false,
        };
    }
}
