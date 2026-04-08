<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;
/**
 * Defines the contract for graphql resolver.
 */
interface GraphqlResolverInterface
{
    /**
     * @param array<string,mixed> $args
     *
     * @return array<string,mixed>|null
     */
    public function category(array $args): ?array;

    /**
     * @param array<string,mixed> $args
     *
     * @return list<array<string,mixed>>
     */
    public function categoryPath(array $args): array;

    /**
     * @param array<string,mixed> $args
     *
     * @return array<string,mixed>|null
     */
    public function publishCategory(array $args): ?array;

    /** @param array<string,mixed> $args */
    public function moveCategory(array $args): bool;
}
