<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\PolicyInterface;

interface CategorySyndicationMappingPolicyInterface
{
    public function assertLocaleMode(string $localeMode): void;

    /**
     * @param array<string,string> $fieldMap
     *
     * @return array<string,string>
     */
    public function normalizeFieldMap(array $fieldMap): array;

    /**
     * @param list<string> $requiredFields
     *
     * @return list<string>
     */
    public function normalizeRequiredFields(array $requiredFields): array;
}
