<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\GraphQl;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

final class CatalogStateProvider implements ProviderInterface
{
    public function __construct(private readonly ProviderInterface $decorated)
    {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        return $this->decorated->provide($operation, $uriVariables, $context);
    }
}
