<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Service;

use App\Cataloging\Entity\Catalog\CatalogCatalogEntity;
use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\Service\CatalogCategoryTypeVocabularyService;
use App\Cataloging\ServiceInterface\CatalogCategoryLookupServiceInterface;
use PHPUnit\Framework\TestCase;

final class CatalogCategoryTypeVocabularyServiceTest extends TestCase
{
    public function testPublishedTypesNormalizesAndDeduplicatesVocabulary(): void
    {
        $category = $this->category([
            'schema' => 'catalog-category-types@1',
            'types' => [
                ['code' => ' UPS ', 'label' => ' UPS '],
                ['code' => 'ups', 'label' => 'Duplicate'],
                ['code' => 'FedEx', 'label' => 'FedEx'],
                ['code' => '', 'label' => 'Invalid'],
                'invalid',
            ],
        ]);

        $service = new CatalogCategoryTypeVocabularyService($this->lookup($category));

        self::assertSame([
            ['code' => 'ups', 'label' => 'UPS'],
            ['code' => 'fedex', 'label' => 'FedEx'],
        ], $service->publishedTypes('shipping', 'providers'));
    }

    public function testRetailingCategorySchemaIsAccepted(): void
    {
        $category = $this->category([
            'schema' => 'retailing-category@1',
            'types' => [['code' => 'tv-mounting', 'label' => 'TV Mounting']],
        ]);

        self::assertSame(
            [['code' => 'tv-mounting', 'label' => 'TV Mounting']],
            (new CatalogCategoryTypeVocabularyService($this->lookup($category)))->publishedTypes('shipping', 'providers'),
        );
    }

    public function testPublishedTypesRejectsUnknownSchema(): void
    {
        $category = $this->category([
            'schema' => 'unknown@1',
            'types' => [['code' => 'ups', 'label' => 'UPS']],
        ]);

        self::assertSame([], (new CatalogCategoryTypeVocabularyService($this->lookup($category)))
            ->publishedTypes('shipping', 'providers'));
    }

    private function category(array $metadata): CatalogCategoryEntity
    {
        $catalog = new CatalogCatalogEntity('shipping', 'Shipping', 'shipping-classification');
        $category = new CatalogCategoryEntity($catalog, 'Providers', 'providers', 'providers', 0);
        $category->setMetadata($metadata);

        return $category;
    }

    private function lookup(CatalogCategoryEntity $category): CatalogCategoryLookupServiceInterface
    {
        return new class($category) implements CatalogCategoryLookupServiceInterface {
            public function __construct(private readonly CatalogCategoryEntity $category)
            {
            }

            public function publishedByCatalogAndPath(
                string $catalogCode,
                string $path,
                string $tenant = 'default',
            ): ?CatalogCategoryEntity {
                TestCase::assertSame('shipping', $catalogCode);
                TestCase::assertSame('providers', $path);
                TestCase::assertSame('default', $tenant);

                return $this->category;
            }
        };
    }
}
