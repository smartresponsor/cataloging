<?php

declare(strict_types=1);

namespace App\Cataloging\Repository\Catalog;

use App\Cataloging\Entity\CatalogCategoryMediaBindingEntity;
use App\Cataloging\EntityInterface\Catalog\CatalogCategoryMediaBindingEntityInterface;
use App\Cataloging\EventInterface\CategoryMediaBoundInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryMediaBindingRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class CatalogCategoryMediaBindingRepository implements CatalogCategoryMediaBindingRepositoryInterface
{
    /** @var array<string,CatalogCategoryMediaBindingEntityInterface> */
    private array $bindings = [];

    /** @var list<CategoryMediaBoundInterface> */
    private array $history = [];

    public function __construct(private readonly ?EntityManagerInterface $entityManager = null)
    {
    }

    public function save(CatalogCategoryMediaBindingEntityInterface $binding): void
    {
        if ($this->entityManager instanceof EntityManagerInterface && $binding instanceof CatalogCategoryMediaBindingEntity) {
            $this->entityManager->persist($binding);
            $this->entityManager->flush();

            return;
        }

        $this->bindings[$binding->bindingId()] = $binding;
    }

    public function find(string $bindingId): ?CatalogCategoryMediaBindingEntityInterface
    {
        if ($this->entityManager instanceof EntityManagerInterface) {
            return $this->entityManager->find(CatalogCategoryMediaBindingEntity::class, trim($bindingId));
        }

        return $this->bindings[$bindingId] ?? null;
    }

    public function bindingsForCategory(string $categoryId): array
    {
        if ($this->entityManager instanceof EntityManagerInterface) {
            return $this->entityManager->getRepository(CatalogCategoryMediaBindingEntity::class)->findBy(['categoryId' => trim($categoryId)]);
        }

        return array_values(array_filter(
            $this->bindings,
            static fn (CatalogCategoryMediaBindingEntityInterface $binding): bool => $binding->categoryId() === $categoryId,
        ));
    }

    public function appendHistory(CategoryMediaBoundInterface $event): void
    {
        $this->history[] = $event;
    }

    public function history(): array
    {
        return $this->history;
    }
}
