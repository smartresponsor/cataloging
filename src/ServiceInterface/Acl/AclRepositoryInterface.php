<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Acl;

interface AclRepositoryInterface
{
    public function put(array $rule): void;

    public function list(array $filter): array;

    public function decide(array $input): bool;
}
