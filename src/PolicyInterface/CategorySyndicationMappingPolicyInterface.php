<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

/**
 * Defines the contract for category syndication mapping policy.
 */
interface CategorySyndicationMappingPolicyInterface
{
    /**
     * Handles the assert locale mode workflow.
     */
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
