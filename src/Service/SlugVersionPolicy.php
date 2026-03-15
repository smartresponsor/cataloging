<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class SlugVersionPolicy
{
    public function next(string $base, int $attempt): string
    {
        $norm = strtolower(trim(preg_replace('/[^a-z0-9-]+/', '-', $base), '-'));

        return $attempt <= 0 ? $norm : $norm.'-'.$attempt;
    }
}
