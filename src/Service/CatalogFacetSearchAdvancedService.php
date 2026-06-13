<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogCategoryProjectionEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides the facet search advanced application service.
 */
final readonly class CatalogFacetSearchAdvancedService
{
    /**
     * Initializes the facet search advanced service collaborators.
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FacetFilter $filter,
        private FacetRank $rank,
    ) {
    }

    /** @return list<array{id:mixed,slug:mixed,name:mixed,path:mixed,locale:mixed}> */
    public function search(
        string $term,
        string $locale = 'en',
        ?string $pathPrefix = null,
        int $limit = 20,
        int $offset = 0,
    ): array {
        $builder = $this->entityManager->createQueryBuilder();
        $builder
            ->select('projection')
            ->from(CatalogCategoryProjectionEntity::class, 'projection')
            ->where('projection.locale = :locale')
            ->andWhere('(projection.slug LIKE :term OR projection.nameEntity LIKE :term)')
            ->setParameter('locale', $locale)
            ->setParameter('term', '%'.$term.'%');

        if (null !== $pathPrefix && '' !== $pathPrefix) {
            $builder
                ->andWhere('projection.path LIKE :pathPrefix')
                ->setParameter('pathPrefix', $pathPrefix.'%');
        }

        /** @var list<CatalogCategoryProjectionEntity> $entities */
        $entities = $builder->getQuery()->getResult();
        $rows = array_map(static fn (CatalogCategoryProjectionEntity $entity): array => [
            'id' => $entity->getId(),
            'slug' => $entity->getSlug(),
            'nameEntity' => $entity->getName(),
            'path' => $entity->getPath(),
            'locale' => $entity->getLocale() ?? '',
        ], $entities);

        $rows = array_values(array_filter(
            $rows,
            fn (array $row): bool => $this->filter->scope($row, $pathPrefix, $locale),
        ));
        usort($rows, fn (array $a, array $b): int => $this->rank->score($term, $b) <=> $this->rank->score($term, $a));

        return array_slice($rows, $offset, $limit);
    }
}
