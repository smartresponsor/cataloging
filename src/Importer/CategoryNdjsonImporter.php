<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Importer;

use App\ImporterInterface\CategoryNdjsonImporterInterface;
use App\ServiceInterface\CategoryServiceInterface as CatalogCategoryService;
use App\ValueObject\CategoryCreateRequest;
use App\ValueObject\CategoryLinkRequest;

/**
 * Provides the category ndjson importer implementation.
 */
final class CategoryNdjsonImporter implements CategoryNdjsonImporterInterface
{
    private CatalogCategoryService $service;

    /**
     * Initializes the category ndjson importer service collaborators.
     */
    public function __construct(CatalogCategoryService $service)
    {
        $this->service = $service;
    }

    /** @return array{ok:int,fail:int,warnings:int,report:list<string>} */
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
                            $this->service->create($this->createRequest($data));
                        }
                        ++$ok;
                        continue;
                    }

                    if ('link' === $type) {
                        if (!$dryRun) {
                            $this->service->attach($this->linkRequest($data));
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
        $type = $this->requiredStringValue($data, 'type');
        if ('' === trim($type)) {
            throw new \InvalidArgumentException('Invalid row');
        }

        return $type;
    }

    /** @param array<string,mixed> $data */
    private function requiredStringValue(array $data, string $key): string
    {
        $value = $this->optionalStringValue($data, $key);
        if (null === $value) {
            throw new \InvalidArgumentException(sprintf('Missing required string key: %s', $key));
        }

        return $value;
    }

    /** @param array<string,mixed> $data */
    private function optionalStringValue(array $data, string $key): ?string
    {
        if (!array_key_exists($key, $data)) {
            return null;
        }
        $value = $data[$key];
        if (!is_scalar($value)) {
            return null;
        }
        $normalized = trim((string) $value);

        return '' === $normalized ? null : $normalized;
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return array<string,string>
     */
    private function stringMapValue(array $data, string $key): array
    {
        $value = $data[$key] ?? null;
        if (!is_array($value)) {
            throw new \InvalidArgumentException(sprintf('Missing required map key: %s', $key));
        }
        $normalized = [];
        foreach ($value as $entryKey => $entryValue) {
            if (!is_string($entryKey) || !is_scalar($entryValue)) {
                continue;
            }
            $normalized[$entryKey] = trim((string) $entryValue);
        }

        return $normalized;
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return array<string,array<string,bool|float|int|string|null>|bool|float|int|string|null>
     */
    private function metaMapValue(array $data, string $key): array
    {
        $value = $data[$key] ?? [];
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $entryKey => $entryValue) {
            if (!is_string($entryKey)) {
                continue;
            }
            if (is_array($entryValue)) {
                $nested = [];
                foreach ($entryValue as $nestedKey => $nestedValue) {
                    if (!is_string($nestedKey)) {
                        continue;
                    }
                    if (
                        is_bool($nestedValue)
                        || is_float($nestedValue)
                        || is_int($nestedValue)
                        || is_string($nestedValue)
                        || null === $nestedValue
                    ) {
                        $nested[$nestedKey] = $nestedValue;
                    }
                }
                $normalized[$entryKey] = $nested;
                continue;
            }
            if (
                is_bool($entryValue)
                || is_float($entryValue)
                || is_int($entryValue)
                || is_string($entryValue)
                || null === $entryValue
            ) {
                $normalized[$entryKey] = $entryValue;
            }
        }

        return $normalized;
    }

    /** @param array<string,mixed> $data */
    private function createRequest(array $data): CategoryCreateRequest
    {
        return new CategoryCreateRequest(
            $this->optionalStringValue($data, 'actorId') ?? 'system',
            $this->requiredStringValue($data, 'taxonomyId'),
            $this->optionalStringValue($data, 'parentId'),
            $this->stringMapValue($data, 'name'),
            $this->stringMapValue($data, 'slug'),
            $this->metaMapValue($data, 'meta'),
        );
    }

    /** @param array<string,mixed> $data */
    private function linkRequest(array $data): CategoryLinkRequest
    {
        return new CategoryLinkRequest(
            $this->optionalStringValue($data, 'actorId') ?? 'system',
            $this->requiredStringValue($data, 'categoryId'),
            $this->requiredStringValue($data, 'targetDomain'),
            $this->requiredStringValue($data, 'targetClass'),
            $this->requiredStringValue($data, 'targetId'),
        );
    }
}
