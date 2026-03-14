<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\ServiceInterface\Import\Category;

interface CollectionImportServiceInterface
{
    public function importCsv(string $file): int;

    public function importNdjson(string $file): int;
}
