<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ImporterInterface;

interface CategoryNdjsonImporterInterface
{
    /**
     * Import NDJSON file.
     *
     * @param string $path   path to NDJSON
     * @param bool   $dryRun do not persist, collect report only
     *
     * @return array{ok:int, fail:int, warnings:int, report:list<string>}
     */
    public function import(string $path, bool $dryRun = true): array;
}
