<?php

declare(strict_types=1);

namespace App\ServiceInterface;

interface CatalogReadServiceInterface
{
    public function byId(string $id): ?array;

    public function childList(string $id): ?array;

    public function ancestorList(string $id): ?array;

    public function descendantsTree(string $id): ?array;

    public function list(int $first, string $after): array;
}
