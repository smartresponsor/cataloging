<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\RepositoryInterface;

interface VirtualCategoryRepositoryInterface
{
    /** @return array{id:string,name:string,rule:array<string,mixed>}|null */
    public function findById(string $id): ?array;
}
