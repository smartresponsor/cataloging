<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Service\CatalogFacetIndexBuilderService;
use App\Cataloging\Service\CatalogSearchService;
use PHPUnit\Framework\TestCase;

final class CatalogSearchServiceTest extends TestCase
{
    public function testSourceUsesProjectionBackedSearchWithoutFileWrites(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2).'/src/Service/CatalogSearchService.php');
        self::assertIsString($source);
        self::assertStringContainsString('category_projection', $source);
        self::assertStringNotContainsString('private array $data', $source);
        self::assertStringNotContainsString('file_put_contents', $source);
    }

    public function testClassExists(): void
    {
        self::assertTrue(class_exists(CatalogSearchService::class));
    }

    public function testFacetCountContractUsesStableIdentifiersAndDeterministicOrdering(): void
    {
        $contract = (new CatalogFacetIndexBuilderService())->buildCountContract(' Workflow_State ', [
            'Published' => 2,
            'Draft' => 3,
            'Archived' => 3,
        ]);

        self::assertSame('workflow_state', $contract['identifier']);
        self::assertSame([
            'archived' => 3,
            'draft' => 3,
            'published' => 2,
        ], $contract['buckets']);
    }

    public function testFacetCountContractRejectsInvalidCount(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new CatalogFacetIndexBuilderService())->buildCountContract('published', ['true' => -1]);
    }
}
