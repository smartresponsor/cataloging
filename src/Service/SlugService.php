<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
/**
 * Provides the slug service application service.
 */
final class SlugService
{
    /**
     * Initializes the slug service service collaborators.
     */
    public function __construct(private readonly Connection $conn)
    {
    }
    /**
     * Handles the ensure unique workflow.
     */
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
