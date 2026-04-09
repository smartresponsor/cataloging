<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Category;
/**
 * Defines the contract for category import service.
 */
interface CategoryImportServiceInterface
{
    /**
     * Handles the import csv workflow.
     */
    public function importCsv(string $file): int;
    /**
     * Handles the import ndjson workflow.
     */
    public function importNdjson(string $file): int;
}
