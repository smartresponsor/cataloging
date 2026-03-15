<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Layer\Category;

final class TreeLock
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function acquire(string $key): void
    {
        $stmt = $this->pdo->prepare('SELECT pg_advisory_lock(hashtext(:k))');
        $stmt->bindValue(':k', $key);
        $stmt->execute();
    }

    public function release(string $key): void
    {
        $stmt = $this->pdo->prepare('SELECT pg_advisory_unlock(hashtext(:k))');
        $stmt->bindValue(':k', $key);
        $stmt->execute();
    }
}
