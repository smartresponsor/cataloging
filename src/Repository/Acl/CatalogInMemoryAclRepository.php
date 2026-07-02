<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Cataloging\Repository\Acl;

use App\Cataloging\ServiceInterface\Acl\AclRepositoryInterface;

final class CatalogInMemoryAclRepository implements AclRepositoryInterface
{
    /** @var list<array<string, mixed>> */
    private array $ruleList = [];

    /** @param array<string, mixed> $rule */
    public function put(array $rule): void
    {
        $this->ruleList[] = $rule;
    }

    /**
     * @param array<string, mixed> $filter
     *
     * @return list<array<string, mixed>>
     */
    public function list(array $filter): array
    {
        return array_values(array_filter(
            $this->ruleList,
            static fn (array $rule): bool => self::matches($rule, $filter),
        ));
    }

    /** @param array<string, mixed> $input */
    public function decide(array $input): bool
    {
        foreach ($this->ruleList as $rule) {
            if (!self::matches($rule, $input)) {
                continue;
            }

            $effect = strtolower((string) ($rule['effect'] ?? $rule['decision'] ?? 'deny'));

            return in_array($effect, ['allow', 'allowed', 'grant', 'granted'], true);
        }

        return false;
    }

    /**
     * @param array<string, mixed> $rule
     * @param array<string, mixed> $filter
     */
    private static function matches(array $rule, array $filter): bool
    {
        foreach ($filter as $key => $value) {
            if (null === $value || '' === $value) {
                continue;
            }

            if (!array_key_exists($key, $rule)) {
                return false;
            }

            if ((string) $rule[$key] !== (string) $value) {
                return false;
            }
        }

        return true;
    }
}
