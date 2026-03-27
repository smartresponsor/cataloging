<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\GraphQl;

use ApiPlatform\GraphQl\Resolver\QueryCollectionResolverInterface;
use App\Entity\CategoryEntity;
use App\Repository\CatalogRepository;
use Doctrine\ORM\EntityManagerInterface;

final class CategoryChildListResolver implements QueryCollectionResolverInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CatalogRepository $repo,
    ) {
    }

    /** @param array<string, mixed> $context */
    public function __invoke(iterable $collection, array $context): iterable
    {
        $argsRaw = $context['args'] ?? [];
        $args = is_array($argsRaw) ? $argsRaw : [];
        $id = is_scalar($args['id'] ?? null) ? (string) $args['id'] : '';

        if ('' === $id) {
            return [];
        }

        /** @var CategoryEntity|null $node */
        $node = $this->em->getRepository(CategoryEntity::class)->find($id);

        return null !== $node ? $this->repo->findChildrenLtree($node) : [];
    }
}

final class CategoryAncestorListResolver implements QueryCollectionResolverInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CatalogRepository $repo,
    ) {
    }

    /** @param array<string, mixed> $context */
    public function __invoke(iterable $collection, array $context): iterable
    {
        $argsRaw = $context['args'] ?? [];
        $args = is_array($argsRaw) ? $argsRaw : [];
        $id = is_scalar($args['id'] ?? null) ? (string) $args['id'] : '';

        if ('' === $id) {
            return [];
        }

        /** @var CategoryEntity|null $node */
        $node = $this->em->getRepository(CategoryEntity::class)->find($id);

        return null !== $node ? $this->repo->findAncestorsLtree($node) : [];
    }
}
