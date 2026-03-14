<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\GraphQl;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\CategoryEntity;
use Doctrine\ORM\EntityManagerInterface;

final class CategoryStateProvider implements ProviderInterface
{
    public function __construct(
        private readonly ProviderInterface $decorated,
        private readonly CategoryRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data = $this->decorated->provide($operation, $uriVariables, $context);
        if ($data instanceof CategoryEntity) {
            return $data;
        }

        return $data;
    }
}
