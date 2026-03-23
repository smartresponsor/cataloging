<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

interface CollectionRuleServiceInterface
{
    public function evaluate(array $dsl, int $limit): array;
}
