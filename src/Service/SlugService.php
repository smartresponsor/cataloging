<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/**
 * Provides the slug service application service.
 */
final readonly class SlugService
{
    /**
     * Initializes the slug service service collaborators.
     */
    public function __construct(private Connection $connection)
    {
    }

    /**
     * Handles the ensure unique workflow.
     *
     * @throws Exception
     */
    public function ensureUnique(string $slug): string
    {
        $baseSlug = $slug;
        $suffix = 2;
        while ($this->exists($slug)) {
            $slug = $baseSlug.'-'.$suffix;
            ++$suffix;
        }

        return $slug;
    }

    /**
     * @throws Exception
     */
    private function exists(string $slug): bool
    {
        $result = $this->connection->fetchOne('SELECT 1 FROM category WHERE slug = ?', [$slug]);

        return (bool) $result;
    }
}
