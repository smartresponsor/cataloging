<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Service\Import\Category;

use App\ServiceInterface\Import\Category\CollectionImportServiceInterface;

final class CollectionImportService implements CollectionImportServiceInterface
{
    public function __construct(private readonly ImportService $importService)
    {
    }

    public function importCsv(string $file): int
    {
        return $this->importService->importCsv($file);
    }

    public function importNdjson(string $file): int
    {
        return $this->importService->importNdjson($file);
    }
}
