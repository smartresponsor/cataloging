<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;

interface CatalogCategoryLookupServiceInterface
{
    public function publishedByCatalogAndSlug(string $catalogCode, string $slug, string $tenant = 'default'): ?CatalogCategoryEntity;
}
