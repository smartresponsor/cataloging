<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\ServiceInterface;

interface GraphqlResolverInterface
{
    public function category(array $args): ?array;

    public function categoryPath(array $args): array;

    public function publishCategory(array $args): ?array;

    public function moveCategory(array $args): bool;
}
