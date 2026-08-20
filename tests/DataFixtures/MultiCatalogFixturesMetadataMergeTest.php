<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\DataFixtures;

use App\Cataloging\DataFixtures\MultiCatalogFixtures;
use PHPUnit\Framework\TestCase;

final class MultiCatalogFixturesMetadataMergeTest extends TestCase
{
    public function testFixtureMetadataMergePreservesUnknownKeysAndTypes(): void
    {
        $fixture = new MultiCatalogFixtures();
        $method = new \ReflectionMethod($fixture, 'mergeCategoryMetadata');

        $merged = $method->invoke($fixture, [
            'schema' => 'catalog-category-types@1',
            'source' => 'runtime',
            'types' => [
                ['code' => 'custom', 'label' => 'Custom', 'enabled' => false],
                ['code' => 'damaged', 'label' => 'Old damaged label', 'note' => 'keep'],
            ],
        ], [
            'schema' => 'catalog-category-types@1',
            'types' => [
                ['code' => 'damaged', 'label' => 'Damaged'],
                ['code' => 'wrong_item', 'label' => 'Wrong Item'],
            ],
        ]);

        self::assertSame('runtime', $merged['source']);
        self::assertSame([
            ['code' => 'custom', 'label' => 'Custom', 'enabled' => false],
            ['code' => 'damaged', 'label' => 'Damaged', 'note' => 'keep'],
            ['code' => 'wrong_item', 'label' => 'Wrong Item'],
        ], $merged['types']);
    }
}
