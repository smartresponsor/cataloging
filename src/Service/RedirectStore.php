<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\RedirectStoreInterface;
use App\ValueObject\RedirectPutRequest;

/**
 * Provides the redirect store application service.
 */
final class RedirectStore implements RedirectStoreInterface
{
    private \PDO $pdo;

    /**
     * Initializes the redirect store service collaborators.
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Handles the put workflow.
     */
    public function put(RedirectPutRequest $request): void
    {
        $q = $this->pdo->prepare(
            'INSERT INTO seo_redirect(from_path, to_path, status) VALUES(:f,:t,:s) '
            .'ON CONFLICT (from_path) DO UPDATE SET to_path = EXCLUDED.to_path, status = EXCLUDED.status',
        );
        $q->bindValue(':f', $request->from());
        $q->bindValue(':t', $request->to());
        $q->bindValue(':s', $request->status());
        $q->execute();
    }

    /** @return array{from:string,to:string,status:int}|null */
    public function get(string $from): ?array
    {
        $q = $this->pdo->prepare('SELECT from_path, to_path, status FROM seo_redirect WHERE from_path = :f LIMIT 1');
        $q->bindValue(':f', $from);
        $q->execute();
        /** @var array<string, mixed>|false $r */
        $r = $q->fetch(\PDO::FETCH_ASSOC);
        if (false === $r) {
            return null;
        }

        return [
            'from' => $this->stringValue($r, 'from_path'),
            'to' => $this->stringValue($r, 'to_path'),
            'status' => $this->intValue($r, 'status'),
        ];
    }

    /** @param array<string, mixed> $row */
    private function stringValue(array $row, string $key): string
    {
        $value = $row[$key] ?? '';

        return is_scalar($value) ? (string) $value : '';
    }

    /** @param array<string, mixed> $row */
    private function intValue(array $row, string $key): int
    {
        $value = $row[$key] ?? 0;

        return is_numeric($value) ? (int) $value : 0;
    }
}
