<?php

declare(strict_types=1);

namespace App\Service\Security;

use App\Security\ExternalIdentityContext;
use App\ServiceInterface\Security\SecurityExternalIdentityContextMapperInterface;
/**
 * Provides the external identity context mapper application service.
 */
final class ExternalIdentityContextMapper implements SecurityExternalIdentityContextMapperInterface
{
    /** @param array<string,mixed> $claims */
    public function map(array $claims): ExternalIdentityContext
    {
        $subject = $this->firstScalarString($claims, ['sub', 'subject', 'user_id']);
        if ('' === $subject) {
            throw new \InvalidArgumentException('External identity claims must include sub.');
        }

        $tenant = $this->nullableScalarString($claims, ['tenant', 'tenant_id', 'org', 'organization_id']);
        $frameworkRoles = $this->normalizeFrameworkRoles($claims);
        $categoryRoles = $this->normalizeCategoryRoles($claims);

        return new ExternalIdentityContext($subject, $tenant, $frameworkRoles, $categoryRoles);
    }

    /** @param array<string,mixed> $claims @return list<string> */
    private function normalizeFrameworkRoles(array $claims): array
    {
        $roles = ['ROLE_USER'];

        foreach ($this->claimValues($claims, ['roles', 'role', 'scope_roles']) as $value) {
            $normalized = strtoupper(trim($value));
            if ('' === $normalized) {
                continue;
            }
            if (!str_starts_with($normalized, 'ROLE_')) {
                $normalized = 'ROLE_' . $normalized;
            }
            $roles[] = $normalized;
        }

        return $this->uniqueSorted($roles);
    }

    /** @param array<string,mixed> $claims @return list<string> */
    private function normalizeCategoryRoles(array $claims): array
    {
        $resolved = [];
        foreach ($this->claimValues($claims, ['category_roles', 'sr_role', 'catalog_roles']) as $value) {
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
     * @param list<string> $keys
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

    /** @param array<string,mixed> $claims @param list<string> $keys */
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

    /** @param array<string,mixed> $claims @param list<string> $keys */
    private function nullableScalarString(array $claims, array $keys): ?string
    {
        $value = $this->firstScalarString($claims, $keys);

        return '' === $value ? null : $value;
    }

    /** @param list<string> $values @return list<string> */
    private function uniqueSorted(array $values): array
    {
        $values = array_values(array_unique($values));
        sort($values, SORT_STRING);

        return $values;
    }
}
