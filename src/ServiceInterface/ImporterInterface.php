<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

/**
 * Defines the contract for importer.
 */
interface ImporterInterface
{
    /**
     * Handles the import csv workflow.
     */
    public function importCsv(string $path): int;

    /**
     * Handles the import json workflow.
     */
    public function importJson(string $path): int;
}
