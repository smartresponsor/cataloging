<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Rule;

final class CategoryRule
{
    /** @var array{all:list<array<string,mixed>>} */
    private array $spec;

    /** @param array<string,mixed> $spec */
    public function __construct(array $spec)
    {
        $this->spec = $this->normalize($spec);
    }

    /** @return array{all:list<array<string,mixed>>} */
    public function spec(): array
    {
        return $this->spec;
    }

    /** @param array<string,mixed> $spec
     * @return array{all:list<array<string,mixed>>}
     */
    private function normalize(array $spec): array
    {
        $all = $spec['all'] ?? null;
        if (!is_array($all)) {
            throw new \InvalidArgumentException('Rule must contain "all" as an array.');
        }
        if (count($all) > CategoryRulePolicy::MAX_CONDITIONS) {
            throw new \InvalidArgumentException('Too many conditions.');
        }
        $normalized = [];
        foreach ($all as $cond) {
            if (!is_array($cond)) {
                throw new \InvalidArgumentException('Condition must be an object.');
            }
            if (isset($cond['attr'])) {
                if (!isset($cond['op']) || !array_key_exists('value', $cond)) {
                    throw new \InvalidArgumentException('Attr condition must contain op and value.');
                }
                $attr = is_scalar($cond['attr']) ? (string) $cond['attr'] : '';
                $op = is_scalar($cond['op']) ? (string) $cond['op'] : '';
                if (!in_array($attr, CategoryRulePolicy::$allowedAttrs, true)) {
                    throw new \InvalidArgumentException('Attr not allowed: '.$attr);
                }
                if (!in_array($op, CategoryRulePolicy::$allowedOps, true)) {
                    throw new \InvalidArgumentException('Op not allowed: '.$op);
                }
            } elseif (!isset($cond['tag'])) {
                throw new \InvalidArgumentException('Unsupported condition object.');
            }

            $normalized[] = $cond;
        }

        return ['all' => $normalized];
    }
}
