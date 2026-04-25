<?php

declare(strict_types=1);

namespace App\Cataloging\Repository;

use App\Cataloging\Entity\CatalogCategoryReviewAssignmentEntity;
use App\Cataloging\EntityInterface\CatalogCategoryReviewAssignmentEntityInterface;
use App\Cataloging\RepositoryInterface\CatalogCategoryReviewAssignmentEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class CatalogCategoryReviewAssignmentEntityRepository implements CatalogCategoryReviewAssignmentEntityRepositoryInterface
{
    /** @var array<string,CatalogCategoryReviewAssignmentEntityInterface> */
    private array $assignments = [];

    public function __construct(private readonly ?EntityManagerInterface $entityManager = null)
    {
    }

    public function save(CatalogCategoryReviewAssignmentEntityInterface $assignment): void
    {
        if ($this->entityManager instanceof EntityManagerInterface && $assignment instanceof CatalogCategoryReviewAssignmentEntity) {
            $this->entityManager->persist($assignment);
            $this->entityManager->flush();

            return;
        }

        $this->assignments[$assignment->requestId()] = $assignment;
    }

    public function findByRequestId(string $requestId): ?CatalogCategoryReviewAssignmentEntityInterface
    {
        if ($this->entityManager instanceof EntityManagerInterface) {
            return $this->entityManager->find(CatalogCategoryReviewAssignmentEntity::class, trim($requestId));
        }

        return $this->assignments[$requestId] ?? null;
    }

    public function findByReviewer(string $reviewer): array
    {
        if ($this->entityManager instanceof EntityManagerInterface) {
            return $this->entityManager->getRepository(CatalogCategoryReviewAssignmentEntity::class)->findBy(['assignedReviewer' => trim($reviewer)]);
        }

        return array_values(array_filter(
            $this->assignments,
            static fn (CatalogCategoryReviewAssignmentEntityInterface $assignment): bool => $assignment->assignedReviewer() === $reviewer,
        ));
    }

    public function findByCategoryId(string $categoryId): array
    {
        if ($this->entityManager instanceof EntityManagerInterface) {
            return $this->entityManager->getRepository(CatalogCategoryReviewAssignmentEntity::class)->findBy(['categoryId' => trim($categoryId)]);
        }

        return array_values(array_filter(
            $this->assignments,
            static fn (CatalogCategoryReviewAssignmentEntityInterface $assignment): bool => $assignment->categoryId() === $categoryId,
        ));
    }
}
if (!class_exists(__NAMESPACE__.'\\CategoryReviewAssignmentRepository', false)) {
    class_alias(CatalogCategoryReviewAssignmentEntityRepository::class, __NAMESPACE__.'\\CategoryReviewAssignmentRepository');
}
