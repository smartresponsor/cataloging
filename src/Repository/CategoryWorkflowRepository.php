<?php

declare(strict_types=1);

namespace App\Cataloging\Repository;

use App\Cataloging\Entity\CatalogCategoryWorkflowEntity;
use App\Cataloging\EntityInterface\CatalogCategoryWorkflowEntityInterface;
use App\Cataloging\EventInterface\CatalogCategoryWorkflowEntityTransitionedInterface;
use App\Cataloging\RepositoryInterface\CatalogCategoryWorkflowEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class CatalogCategoryWorkflowEntityRepository implements CatalogCategoryWorkflowEntityRepositoryInterface
{
    /** @var array<string,CatalogCategoryWorkflowEntityInterface> */
    private array $current = [];

    /** @var array<string,list<CatalogCategoryWorkflowEntityTransitionedInterface>> */
    private array $history = [];

    public function __construct(private readonly ?EntityManagerInterface $entityManager = null)
    {
    }

    public function findByCategoryId(string $categoryId): ?CatalogCategoryWorkflowEntityInterface
    {
        if ($this->entityManager instanceof EntityManagerInterface) {
            return $this->entityManager->find(CatalogCategoryWorkflowEntity::class, trim($categoryId));
        }

        return $this->current[$categoryId] ?? null;
    }

    public function save(CatalogCategoryWorkflowEntityInterface $workflow): void
    {
        if ($this->entityManager instanceof EntityManagerInterface && $workflow instanceof CatalogCategoryWorkflowEntity) {
            $this->entityManager->persist($workflow);
            $this->entityManager->flush();

            return;
        }

        $this->current[$workflow->categoryId()] = $workflow;
    }

    public function appendHistory(CatalogCategoryWorkflowEntityTransitionedInterface $event): void
    {
        $payload = $event->payload();
        $categoryId = isset($payload['categoryId']) && is_scalar($payload['categoryId']) ? trim((string) $payload['categoryId']) : '';
        $this->history[$categoryId] ??= [];
        $this->history[$categoryId][] = $event;
    }

    public function historyForCategoryId(string $categoryId): array
    {
        return $this->history[$categoryId] ?? [];
    }
}
if (!class_exists(__NAMESPACE__.'\\CategoryWorkflowRepository', false)) {
    class_alias(CatalogCategoryWorkflowEntityRepository::class, __NAMESPACE__.'\\CategoryWorkflowRepository');
}
