<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogCatalogEntity;
use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\ServiceInterface\CatalogCategoryLookupServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CatalogCategoryLookupService implements CatalogCategoryLookupServiceInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function publishedByCatalogAndSlug(string $catalogCode, string $slug, string $tenant = 'default'): ?CatalogCategoryEntity
    {
        $catalogCode = trim($catalogCode);
        $slug = trim($slug);
        $tenant = '' === trim($tenant) ? 'default' : trim($tenant);
        if ('' === $catalogCode || '' === $slug) {
            return null;
        }

        $catalogId = $this->entityManager->getConnection()->fetchOne(
            'SELECT id FROM catalog WHERE object_code = :code AND tenant = :tenant ORDER BY id LIMIT 1',
            ['code' => $catalogCode, 'tenant' => $tenant],
        );
        $catalog = false === $catalogId ? null : $this->entityManager->find(CatalogCatalogEntity::class, (int) $catalogId);
        if (!$catalog instanceof CatalogCatalogEntity) {
            return null;
        }

        $category = $this->entityManager->getRepository(CatalogCategoryEntity::class)->findOneBy([
            'catalog' => $catalog,
            'slug' => $slug,
            'tenant' => $tenant,
            'published' => true,
        ]);

        return $category instanceof CatalogCategoryEntity ? $category : null;
    }
}
