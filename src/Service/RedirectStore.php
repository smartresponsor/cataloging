<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
*/

namespace App\Service;

final class RedirectStore
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function put(string $from, string $to, int $status = 301): void
    {
        $q = $this->pdo->prepare('INSERT INTO seo_redirect(from_path, to_path, status) VALUES(:f,:t,:s) ON CONFLICT (from_path) DO UPDATE SET to_path = EXCLUDED.to_path, status = EXCLUDED.status');
        $q->bindValue(':f', $from);
        $q->bindValue(':t', $to);
        $q->bindValue(':s', $status);
        $q->execute();
    }

    public function get(string $from): ?array
    {
        $q = $this->pdo->prepare('SELECT from_path, to_path, status FROM seo_redirect WHERE from_path = :f LIMIT 1');
        $q->bindValue(':f', $from);
        $q->execute();
        $r = $q->fetch(\PDO::FETCH_ASSOC);

        return $r ? ['from' => (string) $r['from_path'], 'to' => (string) $r['to_path'], 'status' => (int) $r['status']] : null;
    }
}
