<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Category;

interface CategoryImportServiceInterface
{
    public function importCsv(string $file): int;

    public function importNdjson(string $file): int;
}
