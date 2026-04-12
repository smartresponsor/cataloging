<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Import;

/**
 * Defines the contract for import repository.
 */
interface ImportRepositoryInterface
{
    /** @param array<string,mixed> $row */
    public function upsertCategory(array $row): void;

    /** @param array<string,mixed> $row */
    public function upsertRule(array $row): void;
}
