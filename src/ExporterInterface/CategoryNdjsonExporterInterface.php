<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ExporterInterface;
/**
 * Defines the contract for category ndjson exporter.
 */
interface CategoryNdjsonExporterInterface
{
    /** Export taxonomy and categories as NDJSON lines to a file. */
    public function export(string $taxonomyCode, string $path, string $locale): void;
}
