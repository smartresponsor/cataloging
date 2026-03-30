<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Exporter;

use App\ExporterInterface\CategoryNdjsonExporterInterface;
use App\RepositoryInterface\CategoryRepositoryInterface;

final class CategoryNdjsonExporter implements CategoryNdjsonExporterInterface
{
    public function __construct(private readonly CategoryRepositoryInterface $repo)
    {
    }

    public function export(string $taxonomyCode, string $path, string $locale): void
    {
        $this->repository();
        $h = fopen($path, 'w');
        if (false === $h) {
            throw new \RuntimeException('Cannot write NDJSON: '.$path);
        }
        // Implement traversal via tree(.., depth=7)
        fwrite($h, json_encode(['type' => 'info', 'taxonomy' => $taxonomyCode, 'exportedAt' => date(DATE_ATOM)])."\n");
        fclose($h);
    }

    private function repository(): CategoryRepositoryInterface
    {
        return $this->repo;
    }
}
