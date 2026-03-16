<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

/** Generates and normalizes locale-dependent slugs; applies conflict auto-suffix when needed. */
interface CatalogtestsSlugGeneratorInterface
{
    /**
     * @param array<string,string> $input localized slug candidates by locale
     *
     * @return array<string,string> normalized slugs after collision policy
     */
    public function generate(array $input, string $taxonomyId, ?string $parentId): array;
}
