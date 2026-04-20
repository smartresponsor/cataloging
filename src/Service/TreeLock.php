<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the tree lock application service.
 */
final class TreeLock
{
    private \PDO $pdo;

    /**
     * Initializes the tree lock service collaborators.
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Handles the acquire workflow.
     */
    public function acquire(string $key): void
    {
        $stmt = $this->pdo->prepare('SELECT pg_advisory_lock(hashtext(:k))');
        $stmt->bindValue(':k', $key);
        $stmt->execute();
    }

    /**
     * Handles the release workflow.
     */
    public function release(string $key): void
    {
        $stmt = $this->pdo->prepare('SELECT pg_advisory_unlock(hashtext(:k))');
        $stmt->bindValue(':k', $key);
        $stmt->execute();
    }
}
