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
    /**
     * @return object|array<int|string, mixed>|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var object|array<int|string, mixed>|null $result */
        $result = $this->decorated->provide($operation, $uriVariables, $context);

        return $result;
    }
}
