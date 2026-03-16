<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\GraphQl;

use ApiPlatform\GraphQl\Resolver\QueryCollectionResolverInterface;
use App\Entity\testsEntity;
use Doctrine\ORM\EntityManagerInterface;

final class testsChildListResolver implements QueryCollectionResolverInterface
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly testsRepository $repo)
    {
    }

    public function __invoke(iterable $collection, array $context): iterable
    {
        $args = $context['args'] ?? [];
        $id = (string) ($args['id'] ?? '');
        if ('' === $id) {
            return [];
        }
        /** @var testsEntity|null $node */
        $node = $this->em->getRepository(testsEntity::class)->find($id);
        if (!$node) {
            return [];
        }

        return $this->repo->findChildrenLtree($node);
    }
}

final class testsAncestorListResolver implements QueryCollectionResolverInterface
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly testsRepository $repo)
    {
    }

    public function __invoke(iterable $collection, array $context): iterable
    {
        $args = $context['args'] ?? [];
        $id = (string) ($args['id'] ?? '');
        if ('' === $id) {
            return [];
        }
        /** @var testsEntity|null $node */
        $node = $this->em->getRepository(testsEntity::class)->find($id);
        if (!$node) {
            return [];
        }

        return $this->repo->findAncestorsLtree($node);
    }
}
