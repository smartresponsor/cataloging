<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogCatalogEntity;
use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\ServiceInterface\CatalogCategoryVocabularyServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CatalogCategoryVocabularyService implements CatalogCategoryVocabularyServiceInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function publishedCategories(string $catalogCode, string $tenant = 'default'): array
    {
        $catalogCode = trim($catalogCode);
        $tenant = '' === trim($tenant) ? 'default' : trim($tenant);
        if ('' === $catalogCode) {
            return [];
        }

        $catalogId = $this->entityManager->getConnection()->fetchOne(
            'SELECT id FROM catalog WHERE object_code = :code AND tenant = :tenant ORDER BY id LIMIT 1',
            ['code' => $catalogCode, 'tenant' => $tenant],
        );
        $catalog = false === $catalogId ? null : $this->entityManager->find(CatalogCatalogEntity::class, (int) $catalogId);
        if (!$catalog instanceof CatalogCatalogEntity) {
            return [];
        }

        $categories = $this->entityManager->getRepository(CatalogCategoryEntity::class)->findBy([
            'catalog' => $catalog,
            'parentId' => null,
            'tenant' => $tenant,
            'workflowState' => 'published',
            'published' => true,
        ], ['nameEntity' => 'ASC']);

        return array_values(array_map(
            static fn (CatalogCategoryEntity $category): array => ['code' => $category->getSlug(), 'label' => $category->getName()],
            $categories,
        ));
    }
}
