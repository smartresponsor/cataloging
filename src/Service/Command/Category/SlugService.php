<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Service\Command\Category;

use Doctrine\DBAL\Connection;

final class SlugService
{
    public function __construct(private readonly Connection $conn)
    {
    }

    public function ensureUnique(string $slug): string
    {
        $base = $slug;
        $i = 2;
        while ($this->exists($slug)) {
            $slug = $base.'-'.$i;
            ++$i;
        }

        return $slug;
    }

    private function exists(string $slug): bool
    {
        $r = $this->conn->fetchOne('SELECT 1 FROM category WHERE slug = ?', [$slug]);

        return (bool) $r;
    }
}
