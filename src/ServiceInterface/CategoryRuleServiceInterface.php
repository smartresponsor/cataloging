<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

interface CategoryRuleServiceInterface
{
    /**
     * @param array<string,mixed> $spec
     *
     * @return array{count:int,sql:string}|null
     */
    public function preview(array $spec): ?array;

    public function apply(string $id): bool;
}
