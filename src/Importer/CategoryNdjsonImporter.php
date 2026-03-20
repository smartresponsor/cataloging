<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Importer;

use App\ImporterInterface\CategoryNdjsonImporterInterface;
use App\ServiceInterface\CategoryInterface as CategoryService;

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

        try {
            while (($line = fgets($h)) !== false) {
                $line = trim($line);
                if ('' === $line) {
                    continue;
                }

                try {
                    $data = $this->decodeRow($line);
                    $type = $this->requireType($data);

                    if ('taxonomy' === $type) {
                        ++$warnings;
                        $report[] = 'taxonomy row skipped';
                        continue;
                    }

                    if ('category' === $type) {
                        if (!$dryRun) {
                            $actorId = isset($data['actorId']) ? (string) $data['actorId'] : 'system';
                            $this->service->create(
                                $actorId,
                                (string) $data['taxonomyId'],
                                isset($data['parentId']) ? (string) $data['parentId'] : null,
                                (array) $data['name'],
                                (array) $data['slug'],
                                (array) ($data['meta'] ?? [])
                            );
                        }
                        ++$ok;
                        continue;
                    }

                    if ('link' === $type) {
                        if (!$dryRun) {
                            $actorId = isset($data['actorId']) ? (string) $data['actorId'] : 'system';
                            $this->service->attach(
                                $actorId,
                                (string) $data['categoryId'],
                                (string) $data['targetDomain'],
                                (string) $data['targetClass'],
                                (string) $data['targetId']
                            );
                        }
                        ++$ok;
                        continue;
                    }

                    ++$fail;
                    $report[] = 'Unknown type: '.$type;
                } catch (\JsonException|\InvalidArgumentException|\RuntimeException|\TypeError $e) {
                    ++$fail;
                    error_log('[CategoryNdjsonImporter] '.$e->getMessage());
                    $report[] = 'Error: '.$e->getMessage();
                }
            }
        } finally {
            fclose($h);
        }

        return ['ok' => $ok, 'fail' => $fail, 'warnings' => $warnings, 'report' => $report];
    }

    /** @return array<string,mixed> */
    private function decodeRow(string $line): array
    {
        $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid row');
        }

        return $data;
    }

    /** @param array<string,mixed> $data */
    private function requireType(array $data): string
    {
        $type = $data['type'] ?? null;
        if (!is_string($type) || '' === trim($type)) {
            throw new \InvalidArgumentException('Invalid row');
        }

        return $type;
    }
}
