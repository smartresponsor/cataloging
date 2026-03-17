<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Rule;

final class CategoryRule
{
    /** @var array<string,mixed> */
    private array $spec;

    /**
     * JSON-DSL schema:
     * { "all": [ { "attr":"brand","op":"in","value":["Acme"] }, {"attr":"price","op":"between","value":[10,99]}, {"attr":"stock","op":">","value":0}, {"tag":"promo"} ] }
     */
    public function __construct(array $spec)
    {
        $this->assertValid($spec);
        $this->spec = $spec;
    }

    /** @return array<string,mixed> */
    public function spec(): array
    {
        return $this->spec;
    }

    private function assertValid(array $spec): void
    {
        $this->assertByPolicy($spec);
    }

    private function assertByPolicy(array $spec): void
    {
        if (!isset($spec['all']) || !is_array($spec['all'])) {
            throw new \InvalidArgumentException('Rule must contain "all" as an array.');
        }

        if (count($spec['all']) > CategoryRulePolicy::MAX_CONDITIONS) {
            throw new \InvalidArgumentException('Too many conditions.');
        }

        foreach ($spec['all'] as $cond) {
            if (!is_array($cond)) {
                throw new \InvalidArgumentException('Condition must be an object.');
            }

            if (isset($cond['attr'])) {
                if (!isset($cond['op'])) {
                    throw new \InvalidArgumentException('Missing op for attr condition.');
                }
                if (!array_key_exists('value', $cond)) {
                    throw new \InvalidArgumentException('Missing value for attr condition.');
                }
                if (!in_array($cond['attr'], CategoryRulePolicy::$allowedAttrs, true)) {
                    throw new \InvalidArgumentException('Attr not allowed: '.$cond['attr']);
                }
                if (!in_array($cond['op'], CategoryRulePolicy::$allowedOps, true)) {
                    throw new \InvalidArgumentException('Op not allowed: '.$cond['op']);
                }
                if (
                    'price' === $cond['attr']
                    && 'between' === $cond['op']
                    && is_array($cond['value'])
                    && 2 === count($cond['value'])
                ) {
                    [$a, $b] = $cond['value'];
                    if ($a < CategoryRulePolicy::PRICE_MIN || $b > CategoryRulePolicy::PRICE_MAX) {
                        throw new \InvalidArgumentException('Price range out of bounds.');
                    }
                }
            } elseif (!isset($cond['tag'])) {
                throw new \InvalidArgumentException('Unsupported condition object.');
            }
        }
    }
}
