<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationMappingPolicyInterface;

final class CategorySyndicationMappingPolicy implements CategorySyndicationMappingPolicyInterface
{
    public function assertLocaleMode(string $localeMode): void
    {
        $allowed = ['per_locale', 'shared', 'destination_default'];
        if (!in_array(trim($localeMode), $allowed, true)) {
            throw new \InvalidArgumentException(sprintf('Unsupported syndication locale mode "%s".', $localeMode));
        }
    }

    public function normalizeFieldMap(array $fieldMap): array
    {
        $normalized = [];
        foreach ($fieldMap as $sourceField => $targetField) {
            $source = trim((string) $sourceField);
            $target = trim((string) $targetField);
            if ('' === $source || '' === $target) {
                continue;
            }

            $normalized[$source] = $target;
        }

        ksort($normalized);

        return $normalized;
    }

    public function normalizeRequiredFields(array $requiredFields): array
    {
        $normalized = [];
        foreach ($requiredFields as $requiredField) {
            $value = trim((string) $requiredField);
            if ('' === $value) {
                continue;
            }

            $normalized[] = $value;
        }

        $normalized = array_values(array_unique($normalized));
        sort($normalized);

        return $normalized;
    }
}
