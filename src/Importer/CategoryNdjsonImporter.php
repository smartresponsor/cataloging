<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Importer;

use App\ImporterInterface\CategoryNdjsonImporterInterface;
use App\ServiceInterface\CatalogCategoryInterface as CategoryService;

final class CategoryNdjsonImporter implements CategoryNdjsonImporterInterface
{
    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function import(string $path, bool $dryRun = true): array
    {
        $ok = 0;
        $fail = 0;
        $warnings = 0;
        $report = [];
        $h = fopen($path, 'r');
        if (false === $h) {
            throw new \RuntimeException('Cannot open NDJSON: '.$path);
        }
        while (($line = fgets($h)) !== false) {
            $line = trim($line);
            if ('' === $line) {
                continue;
            }
            $data = json_decode($line, true);
            if (!is_array($data) || empty($data['type'])) {
                ++$fail;
                $report[] = 'Invalid row';
                continue;
            }
            try {
                if ('taxonomy' === $data['type']) {
                    // taxonomy create handled elsewhere; assume exists for now
                    ++$warnings;
                } elseif ('category' === $data['type']) {
                    if (!$dryRun) {
                        $actorId = $data['actorId'] ?? 'system';
                        $this->service->create(
                            $actorId,
                            (string) $data['taxonomyId'],
                            $data['parentId'] ?? null,
                            (array) $data['name'],
                            (array) $data['slug'],
                            (array) ($data['meta'] ?? [])
                        );
                    }
                    ++$ok;
                } elseif ('link' === $data['type']) {
                    if (!$dryRun) {
                        $actorId = $data['actorId'] ?? 'system';
                        $this->service->attach($actorId, (string) $data['categoryId'], (string) $data['targetDomain'], (string) $data['targetClass'], (string) $data['targetId']);
                    }
                    ++$ok;
                } else {
                    ++$fail;
                    $report[] = 'Unknown type: '.$data['type'];
                }
            } catch (\Throwable $e) {
                ++$fail;
                $report[] = 'Error: '.$e->getMessage();
            }
        }
        fclose($h);

        return ['ok' => $ok, 'fail' => $fail, 'warnings' => $warnings, 'report' => $report];
    }
}
