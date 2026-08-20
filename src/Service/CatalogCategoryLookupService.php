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

    public function publishedByCatalogAndPath(string $catalogCode, string $path, string $tenant = 'default'): ?CatalogCategoryEntity
    {
        $catalogCode = trim($catalogCode);
        $path = trim($path);
        $tenant = '' === trim($tenant) ? 'default' : trim($tenant);
        if ('' === $catalogCode || '' === $path) {
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
            'path' => $path,
            'tenant' => $tenant,
            'workflowState' => 'published',
            'published' => true,
        ]);

        return $category instanceof CatalogCategoryEntity ? $category : null;
    }
}
