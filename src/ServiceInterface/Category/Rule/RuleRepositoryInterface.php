<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 * Canon: single-hyphen names, no plurals in Class/Method, Postgres=Data, MySQL=Infrastructure
 * Tag Signin: <17111337+taa0662621456@users.noreply.github.com>
 */

namespace App\ServiceInterface\Category\Domaine\Rule;

interface RuleRepositoryInterface
{
    public function save(array $rule): string;

    public function find(string $id): ?array;

    public function list(array $opt = []): array;
}
