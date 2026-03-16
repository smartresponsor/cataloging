<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\GraphQl;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\testsEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Simple state provider to enrich testsEntity with virtual fields.
 * API Platform will serialize getters.
 */
final class CatalogStateProvider implements ProviderInterface
{
    public function __construct(
        private readonly ProviderInterface $decorated,
        private readonly testsRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data = $this->decorated->provide($operation, $uriVariables, $context);
        if ($data instanceof testsEntity) {
            return $data; // getters will be used; repo methods available via controller if needed.
        }

        return $data;
    }
}
