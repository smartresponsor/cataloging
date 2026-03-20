<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class ProjectionWorker
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function runOnce(int $limit = 100): int
    {
        $stmt = $this->pdo->prepare('SELECT id, type, payload FROM outbox WHERE processed_at IS NULL ORDER BY created_at ASC LIMIT :l');
        $stmt->bindValue(':l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        foreach ($rows as $row) {
            $this->apply((string) $row['type'], json_decode((string) $row['payload'], true));
            $u = $this->pdo->prepare('UPDATE outbox SET processed_at = NOW() WHERE id = :id AND processed_at IS NULL');
            $u->bindValue(':id', (string) $row['id']);
            $u->execute();
        }

        return count($rows);
    }

    private function apply(string $type, array $payload): void
    {
        // Apply to MySQL projection or cache (infrastructure layer).
    }
}
