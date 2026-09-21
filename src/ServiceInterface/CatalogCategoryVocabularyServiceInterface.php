<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

interface CatalogCategoryVocabularyServiceInterface
{
    /** @return list<array{code: string, label: string}> */
    public function publishedCategories(string $catalogCode, string $tenant = 'default'): array;
}
