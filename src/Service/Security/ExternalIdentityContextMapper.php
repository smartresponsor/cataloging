<?php

declare(strict_types=1);

namespace App\Cataloging\Service\Security;

use App\Cataloging\ServiceInterface\Security\SecurityExternalIdentityContextMapperInterface;
use App\Cataloging\ValueObject\Security\CategoryRole;
use App\Cataloging\ValueObject\Security\ExternalIdentityContext;

/**
 * Provides the external identity context mapper application service.
 */
final class ExternalIdentityContextMapper implements SecurityExternalIdentityContextMapperInterface
{
    private const array SUBJECT_CLAIM_KEYS = ['sub', 'subject', 'user_id'];
    private const array TENANT_CLAIM_KEYS = ['tenant_id', 'tenant', 'tenantId'];
    private const array FRAMEWORK_ROLE_CLAIM_KEYS = ['roles', 'role', 'scope'];
    private const array CATEGORY_ROLE_CLAIM_KEYS = ['category_roles', 'categoryRoles', 'catalog_roles', 'catalogRoles'];

    /** @param array<string,mixed> $claims */
    public function map(array $claims): ExternalIdentityContext
    {
        $subject = $this->firstScalarString($claims, self::SUBJECT_CLAIM_KEYS);
        if ('' === $subject) {
            throw new \InvalidArgumentException('External identity claims must include sub.');
        }

        $tenant = $this->firstScalarString($claims, self::TENANT_CLAIM_KEYS);
        $tenant = '' === $tenant ? null : $tenant;
        $frameworkRoles = $this->normalizeFrameworkRoles($claims);
        $categoryRoles = $this->normalizeCategoryRoles($claims);

        return new ExternalIdentityContext($subject, $tenant, $frameworkRoles, $categoryRoles);
    }

    /**
     * @param array<string,mixed> $claims
     *
     * @return list<string>
     */
    private function normalizeFrameworkRoles(array $claims): array
    {
        $roles = ['ROLE_USER'];

        foreach ($this->claimValues($claims, self::FRAMEWORK_ROLE_CLAIM_KEYS) as $value) {
            $normalized = strtoupper(trim($value));
            if ('' === $normalized) {
                continue;
            }
            if (!str_starts_with($normalized, 'ROLE_')) {
                $normalized = 'ROLE_'.$normalized;
            }
            $roles[] = $normalized;
        }

        return $this->uniqueSorted($roles);
    }

    /**
     * @param array<string,mixed> $claims
     *
     * @return list<string>
     */
    private function normalizeCategoryRoles(array $claims): array
    {
        $resolved = [];
        foreach ($this->claimValues($claims, self::CATEGORY_ROLE_CLAIM_KEYS) as $value) {
            $normalized = strtolower(trim($value));
            $mapped = match ($normalized) {
                'owner', 'category.owner' => CategoryRole::OWNER,
                'editor', 'category.editor' => CategoryRole::EDITOR,
                'publisher', 'publish', 'category.publisher' => CategoryRole::PUBLISHER,
                'reader', 'read', 'category.reader' => CategoryRole::READER,
                'auditor', 'audit', 'category.auditor' => CategoryRole::AUDITOR,
                default => null,
            };
            if (null !== $mapped) {
                $resolved[] = $mapped;
            }
        }

        return $this->uniqueSorted($resolved);
    }

    /**
     * @param array<string,mixed> $claims
     * @param list<string>        $keys
     *
     * @return list<string>
     */
    private function claimValues(array $claims, array $keys): array
    {
        $values = [];
        foreach ($keys as $key) {
            $raw = $claims[$key] ?? null;
            if (is_string($raw)) {
                $parts = preg_split('/[\s,]+/', trim($raw)) ?: [];
                foreach ($parts as $part) {
                    if ('' !== $part) {
                        $values[] = $part;
                    }
                }
                continue;
            }
            if (is_array($raw)) {
                foreach ($raw as $value) {
                    if (is_scalar($value)) {
                        $trimmed = trim((string) $value);
                        if ('' !== $trimmed) {
                            $values[] = $trimmed;
                        }
                    }
                }
            }
        }

        return $values;
    }

    /**
     * @param array<string,mixed> $claims
     * @param list<string>        $keys
     */
    private function firstScalarString(array $claims, array $keys): string
    {
        foreach ($keys as $key) {
            $value = $claims[$key] ?? null;
            if (is_scalar($value)) {
                $trimmed = trim((string) $value);
                if ('' !== $trimmed) {
                    return $trimmed;
                }
            }
        }

        return '';
    }

    /**
     * @param list<string> $values
     *
     * @return list<string>
     */
    private function uniqueSorted(array $values): array
    {
        $values = array_values(array_unique($values));
        sort($values, SORT_STRING);

        return $values;
    }
}
