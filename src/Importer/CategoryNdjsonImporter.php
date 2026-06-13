<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Importer;

use App\Cataloging\ImporterInterface\CategoryNdjsonImporterInterface;
use App\Cataloging\Service\CatalogMetaPayloadNormalizerService;
use App\Cataloging\ServiceInterface\CatalogCategoryServiceInterface as CatalogCategoryService;
use App\Cataloging\ValueObject\CatalogCategoryLinkEntityRequest;
use App\Cataloging\ValueObject\CategoryCreateRequest;

/**
 * Provides the category ndjson importer implementation.
 */
final readonly class CategoryNdjsonImporter implements CategoryNdjsonImporterInterface
{
    /**
     * Initializes the category ndjson importer service collaborators.
     */
    public function __construct(
        private CatalogCategoryService $service,
        private CatalogMetaPayloadNormalizerService $metaPayloadNormalizer,
    ) {
    }

    /** @return array{ok:int,fail:int,warnings:int,report:list<string>} */
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
                } catch (\JsonException|\InvalidArgumentException|\RuntimeException|\TypeError $exception) {
                    ++$fail;
                    error_log('[CategoryNdjsonImporter] '.$exception->getMessage());
                    $report[] = 'Error: '.$exception->getMessage();
                }
            }
        } finally {
            fclose($handle);
        }

        return ['ok' => $ok, 'fail' => $fail, 'warnings' => $warnings, 'report' => $report];
    }

    /**
     * @return array<string,mixed>
     *
     * @throws \JsonException
     * @throws \InvalidArgumentException
     */
    private function decodeRow(string $line): array
    {
        $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid row');
        }

        return $this->normalizeMap($this->metaPayloadNormalizer->normalize($data));
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return array<string,mixed>
     */
    private function normalizeMap(array $data): array
    {
        return $data;
    }

    /**
     * @param array<string,mixed> $data
     *
     * @throws \InvalidArgumentException
     */
    private function requireType(array $data): string
    {
        $type = $this->requiredStringValue($data, 'type');
        if ('' === $type) {
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
    private function metaMapValue(array $data): array
    {
        return $this->metaPayloadNormalizer->normalize($data['meta'] ?? []);
    }

    /** @param array<string,mixed> $data */
    private function createRequest(array $data): CategoryCreateRequest
    {
        return new CategoryCreateRequest(
            $this->optionalStringValue($data, 'actorId') ?? 'system',
            $this->requiredStringValue($data, 'taxonomyId'),
            $this->optionalStringValue($data, 'parentId'),
            $this->stringMapValue($data, 'nameEntity'),
            $this->stringMapValue($data, 'slug'),
            $this->metaMapValue($data),
        );
    }

    /** @param array<string,mixed> $data */
    private function linkRequest(array $data): CatalogCategoryLinkEntityRequest
    {
        return new CatalogCategoryLinkEntityRequest(
            $this->optionalStringValue($data, 'actorId') ?? 'system',
            $this->requiredStringValue($data, 'categoryId'),
            $this->requiredStringValue($data, 'targetDomain'),
            $this->requiredStringValue($data, 'targetClass'),
            $this->requiredStringValue($data, 'targetId'),
        );
    }
}
