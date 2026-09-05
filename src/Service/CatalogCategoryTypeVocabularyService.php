<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ServiceInterface\CatalogCategoryLookupServiceInterface;
use App\Cataloging\ServiceInterface\CatalogCategoryTypeVocabularyServiceInterface;

final readonly class CatalogCategoryTypeVocabularyService implements CatalogCategoryTypeVocabularyServiceInterface
{
    private const SCHEMAS = ['catalog-category-types@1', 'retailing-category@1'];

    public function __construct(private CatalogCategoryLookupServiceInterface $categories)
    {
    }

    public function publishedTypes(string $catalogCode, string $categoryPath, string $tenant = 'default'): array
    {
        $category = $this->categories->publishedByCatalogAndPath($catalogCode, $categoryPath, $tenant);
        if (null === $category) {
            return [];
        }

        $metadata = $category->getMetadata();
        if (!in_array($metadata['schema'] ?? null, self::SCHEMAS, true) || !is_array($metadata['types'] ?? null)) {
            return [];
        }

        $types = [];
        $seen = [];
        foreach ($metadata['types'] as $type) {
            if (!is_array($type)) {
                continue;
            }
            $code = strtolower(trim((string) ($type['code'] ?? '')));
            $label = trim((string) ($type['label'] ?? ''));
            if ('' === $code || '' === $label || isset($seen[$code])) {
                continue;
            }
            $seen[$code] = true;
            $types[] = ['code' => $code, 'label' => $label];
        }

        return $types;
    }
}
