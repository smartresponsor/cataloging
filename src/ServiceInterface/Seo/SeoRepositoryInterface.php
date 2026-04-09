<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Seo;
/**
 * Defines the contract for seo repository.
 */
interface SeoRepositoryInterface
{
    /** @param array<string,mixed> $input */
    public function save(array $input): void;

    /** @return array<string,mixed>|null */
    public function find(string $categoryId, string $locale): ?array;
}
