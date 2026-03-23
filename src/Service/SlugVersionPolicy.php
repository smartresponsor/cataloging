<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class SlugVersionPolicy
{
    public function next(string $base, int $attempt): string
    {
        $norm = strtolower(trim(preg_replace('/[^a-z0-9-]+/', '-', $base), '-'));

        return $attempt <= 0 ? $norm : $norm.'-'.$attempt;
    }
}
