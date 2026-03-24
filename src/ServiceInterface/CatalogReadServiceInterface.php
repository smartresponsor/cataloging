<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

interface CatalogReadServiceInterface
{
    public function childList(string $id): ?array;

    public function ancestorList(string $id): ?array;

    /**
     * @return array{item: array<int,array<string,mixed>>, after: string}
     */
    public function list(int $first, string $after): array;
}
