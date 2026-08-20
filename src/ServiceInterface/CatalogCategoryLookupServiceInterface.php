<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;

interface CatalogCategoryLookupServiceInterface
{
    public function publishedByCatalogAndPath(string $catalogCode, string $path, string $tenant = 'default'): ?CatalogCategoryEntity;
}
