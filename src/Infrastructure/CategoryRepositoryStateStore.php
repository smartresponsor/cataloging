<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Infrastructure;

use App\Repository\CategoryRepository;

final class CategoryRepositoryStateStore
{
    public function save(CategoryRepository $repository, string $file): array
    {
        $rows = $repository->exportState();
        $dir = dirname($file);
        if ('' !== $dir && '.' !== $dir && !is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($file, json_encode([
            'savedAt' => gmdate('c'),
            'count' => count($rows),
            'items' => $rows,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

        return ['file' => $file, 'count' => count($rows)];
    }

    public function load(CategoryRepository $repository, string $file): array
    {
        if (!is_file($file)) {
            return ['file' => $file, 'count' => 0, 'loaded' => false];
        }

        $payload = json_decode((string) file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];
        $repository->importState($items);

        return ['file' => $file, 'count' => count($items), 'loaded' => true];
    }
}
