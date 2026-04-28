<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogCategoryProjectionEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides the facet search application service.
 */
final readonly class CatalogFacetSearchService
{
    /**
     * Initializes the facet search service collaborators.
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /** @return list<array{id:string,slug:string,name:string,path:string,locale:string}> */
    public function search(string $term, string $locale = 'en', int $limit = 20, int $offset = 0): array
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder
            ->select('projection')
            ->from(CatalogCategoryProjectionEntity::class, 'projection')
            ->where('projection.locale = :locale')
            ->andWhere('(projection.slug LIKE :term OR projection.name LIKE :term)')
            ->setParameter('locale', $locale)
            ->setParameter('term', '%'.$term.'%')
            ->orderBy('projection.name', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        /** @var list<CatalogCategoryProjectionEntity> $entities */
        $entities = $builder->getQuery()->getResult();

        return array_map(static fn (CatalogCategoryProjectionEntity $entity): array => [
            'id' => $entity->getId(),
            'slug' => $entity->getSlug(),
            'name' => $entity->getName(),
            'path' => $entity->getPath(),
            'locale' => $entity->getLocale() ?? '',
        ], $entities);
    }
}
