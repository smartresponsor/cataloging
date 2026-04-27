<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Exporter;

use App\Cataloging\ExporterInterface\CategoryNdjsonExporterInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryRepositoryInterface;
use App\Cataloging\ValueObject\CategoryTreeRequest;

/**
 * Provides the category ndjson exporter implementation.
 */
/** @noinspection PhpPropertyOnlyWrittenInspection */
final readonly class CategoryNdjsonExporter implements CategoryNdjsonExporterInterface
{
    /**
     * Initializes the category ndjson exporter service collaborators.
     */
    public function __construct(private CatalogCategoryRepositoryInterface $repository)
    {
    }

    /**
     * Handles the export workflow.
     */
    public function export(string $taxonomyCode, string $path, string $locale): void
    {
        $this->repository->tree(new CategoryTreeRequest($taxonomyCode, null, 1, $locale));
        $handle = fopen($path, 'w');
        if (false === $handle) {
            throw new \RuntimeException('Cannot write NDJSON: '.$path);
        }
        // Implement traversal via tree(.., depth=7)
        fwrite($handle, json_encode(['type' => 'info', 'taxonomy' => $taxonomyCode, 'exportedAt' => date(DATE_ATOM)])."\n");
        fclose($handle);
    }
}
