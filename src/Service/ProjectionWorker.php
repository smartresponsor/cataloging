<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

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
            $payload = $this->decodePayload($row['payload'] ?? null);
            $this->apply((string) $row['type'], $payload);
            $u = $this->pdo->prepare('UPDATE outbox SET processed_at = NOW() WHERE id = :id AND processed_at IS NULL');
            $u->bindValue(':id', (string) $row['id']);
            $u->execute();
        }

        return count($rows);
    }

    /** @param array<string,mixed> $payload */
    private function apply(string $type, array $payload): void
    {
    }

    /** @return array<string,mixed> */
    private function decodePayload(mixed $payload): array
    {
        if (!is_string($payload) || '' === $payload) {
            return [];
        }
        $decoded = json_decode($payload, true);

        return is_array($decoded) ? $decoded : [];
    }
}
