<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Importer;

use App\ImporterInterface\CategoryNdjsonImporterInterface;
use App\ServiceInterface\CatalogCategoryInterface as CategoryService;

final class CategoryNdjsonImporter implements CategoryNdjsonImporterInterface
{
    public function __construct(private readonly CategoryService $service)
    {
    }

    public function import(string $path, bool $dryRun = true): array
    {
        $ok = 0;
        $fail = 0;
        $warnings = 0;
        $report = [];
        $handle = fopen($path, 'r');
        if (false === $handle) {
            throw new \RuntimeException('Cannot open NDJSON: '.$path);
        }

        try {
            while (($line = fgets($handle)) !== false) {
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
                    switch ($data['type']) {
                        case 'taxonomy':
                            ++$warnings;
                            $report[] = 'Taxonomy rows are metadata-only in importer';
                            break;

                        case 'category':
                            if (!$dryRun) {
                                $this->service->create(
                                    (string) $data['taxonomyId'],
                                    isset($data['parentId']) ? (string) $data['parentId'] : null,
                                    (array) ($data['name'] ?? []),
                                    (array) ($data['slug'] ?? []),
                                    (array) ($data['meta'] ?? []),
                                );
                            }
                            ++$ok;
                            break;

                        case 'link':
                            if (!$dryRun) {
                                $this->service->attach(
                                    (string) $data['categoryId'],
                                    (string) $data['targetDomain'],
                                    (string) $data['targetClass'],
                                    (string) $data['targetId'],
                                );
                            }
                            ++$ok;
                            break;

                        default:
                            ++$fail;
                            $report[] = 'Unknown type: '.$data['type'];
                    }
                } catch (\Throwable $e) {
                    ++$fail;
                    $report[] = 'Error: '.$e->getMessage();
                }
            }
        } finally {
            fclose($handle);
        }

        return ['ok' => $ok, 'fail' => $fail, 'warnings' => $warnings, 'report' => $report];
    }
}
