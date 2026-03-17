<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Exporter;

use App\Exporter\CategoryNdjsonExporter;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\TestCase;

final class CategoryNdjsonExporterTest extends TestCase
{
    public function testExportWritesInfoAndCategoryRows(): void
    {
        $repo = new CategoryRepository();
        $root = $repo->create('catalog', null, ['en' => 'Root'], ['en' => 'root'], ['visible' => true]);
        $repo->create('catalog', (string) $root['id'], ['en' => 'Phones'], ['en' => 'phones'], []);

        $path = sys_get_temp_dir().'/category-export-'.uniqid('', true).'.ndjson';
        (new CategoryNdjsonExporter($repo))->export('catalog', $path, 'en');

        $lines = array_values(array_filter(array_map('trim', file($path))));
        self::assertGreaterThanOrEqual(3, count($lines));

        $info = json_decode($lines[0], true, 512, JSON_THROW_ON_ERROR);
        $firstCategory = json_decode($lines[1], true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('info', $info['type']);
        self::assertSame('catalog', $info['taxonomy']);
        self::assertSame('category', $firstCategory['type']);
        self::assertSame('catalog', $firstCategory['taxonomyId']);
        self::assertSame('Root', $firstCategory['name']['en']);
    }
}
