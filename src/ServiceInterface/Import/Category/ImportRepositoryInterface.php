<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\ServiceInterface\Import\Category;

interface ImportRepositoryInterface
{
    public function upsertCategory(array $row): void;

    public function upsertRule(array $row): void;
}
