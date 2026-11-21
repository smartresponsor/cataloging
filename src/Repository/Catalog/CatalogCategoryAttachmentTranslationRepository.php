<?php

declare(strict_types=1);

namespace App\Cataloging\Repository\Catalog;

use App\Cataloging\Entity\Catalog\CatalogCategoryAttachmentTranslationEntity;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryAttachmentTranslationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<CatalogCategoryAttachmentTranslationEntity> */
final class CatalogCategoryAttachmentTranslationRepository extends ServiceEntityRepository implements CatalogCategoryAttachmentTranslationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CatalogCategoryAttachmentTranslationEntity::class);
    }
}
