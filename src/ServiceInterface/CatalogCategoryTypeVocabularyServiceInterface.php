<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

interface CatalogCategoryTypeVocabularyServiceInterface
{
    /** @return list<array{code: string, label: string}> */
    public function publishedTypes(string $catalogCode, string $categoryPath, string $tenant = 'default'): array;
}
