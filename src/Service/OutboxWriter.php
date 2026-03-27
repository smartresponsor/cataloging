<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class OutboxWriter
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @param array<string,mixed> $payload */
    public function append(string $type, array $payload, string $key): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO outbox(id, type, payload, key, created_at) VALUES (gen_random_uuid(), :t, :p, :k, NOW()) ON CONFLICT (key) DO NOTHING');
        $stmt->bindValue(':t', $type);
        $stmt->bindValue(':p', json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        $stmt->bindValue(':k', $key);
        $stmt->execute();
    }
}
