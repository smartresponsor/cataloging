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
        $handle = fopen($path, 'w');
        if (false === $handle) {
            throw new \RuntimeException('Cannot write NDJSON: '.$path);
        }

        try {
            fwrite($handle, json_encode([
                'type' => 'info',
                'taxonomy' => $taxonomyCode,
                'locale' => $locale,
                'exportedAt' => date(DATE_ATOM),
            ], JSON_THROW_ON_ERROR).'
');

            foreach ($this->repo->tree($taxonomyCode, null, 7, $locale) as $row) {
                fwrite($handle, json_encode([
                    'type' => 'category',
                    'id' => $row['id'] ?? null,
                    'taxonomyId' => $row['taxonomyId'] ?? $taxonomyCode,
                    'parentId' => $row['parentId'] ?? null,
                    'name' => [$locale => $row['name'] ?? ''],
                    'slug' => [$locale => $row['slug'] ?? ''],
                    'path' => $row['path'] ?? '',
                    'meta' => $row['meta'] ?? [],
                    'order' => $row['order'] ?? 0,
                ], JSON_THROW_ON_ERROR).'
');
            }
        } finally {
            fclose($handle);
        }
    }
}
