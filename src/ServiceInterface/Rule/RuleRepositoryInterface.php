<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Rule;

interface RuleRepositoryInterface
{
    public function save(array $rule): string;

    public function find(string $id): ?array;

    public function list(array $opt = []): array;
}
