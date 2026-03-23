<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class SurrogateKeyHeader
{
    public function make(array $keys): array
    {
        return ['Surrogate-Key' => implode(' ', array_unique($keys))];
    }
}
