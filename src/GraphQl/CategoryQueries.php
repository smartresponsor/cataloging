<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\GraphQl;

use ApiPlatform\GraphQl\Resolver\QueryCollectionResolverInterface;
use App\Entity\CategoryEntity;
use Doctrine\ORM\EntityManagerInterface;

final class CategoryChildListResolver implements QueryCollectionResolverInterface
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly CategoryRepository $repo)
    {
    }

    public function __invoke(iterable $collection, array $context): iterable
    {
        $args = $context['args'] ?? [];
        $id = (string) ($args['id'] ?? '');
        if ('' === $id) {
            return [];
        }
        /** @var CategoryEntity|null $node */
        $node = $this->em->getRepository(CategoryEntity::class)->find($id);
        if (!$node) {
            return [];
        }

        return $this->repo->findChildrenLtree($node);
    }
}

final class CategoryAncestorListResolver implements QueryCollectionResolverInterface
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly CategoryRepository $repo)
    {
    }

    public function __invoke(iterable $collection, array $context): iterable
    {
        $args = $context['args'] ?? [];
        $id = (string) ($args['id'] ?? '');
        if ('' === $id) {
            return [];
        }
        /** @var CategoryEntity|null $node */
        $node = $this->em->getRepository(CategoryEntity::class)->find($id);
        if (!$node) {
            return [];
        }

        return $this->repo->findAncestorsLtree($node);
    }
}
