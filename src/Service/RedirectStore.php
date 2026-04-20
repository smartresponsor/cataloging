<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ServiceInterface\RedirectStoreInterface;
use App\Cataloging\ValueObject\RedirectPutRequest;

/**
 * Provides the redirect store application service.
 */
final readonly class RedirectStore implements RedirectStoreInterface
{
    private \PDO $connection;

    /**
     * Initializes the redirect store service collaborators.
     */
    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Handles the put workflow.
     */
    public function put(RedirectPutRequest $request): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO seo_redirect(from_path, to_path, status) VALUES(:f,:t,:s) '
            .'ON CONFLICT (from_path) DO UPDATE SET to_path = EXCLUDED.to_path, status = EXCLUDED.status',
        );
        $statement->bindValue(':f', $request->from());
        $statement->bindValue(':t', $request->to());
        $statement->bindValue(':s', $request->status());
        $statement->execute();
    }

    /** @return array{from:string,to:string,status:int}|null */
    public function get(string $from): ?array
    {
        $statement = $this->connection->prepare('SELECT from_path, to_path, status FROM seo_redirect WHERE from_path = :f LIMIT 1');
        $statement->bindValue(':f', $from);
        $statement->execute();
        /** @var array<string, mixed>|false $row */
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        if (false === $row) {
            return null;
        }

        return [
            'from' => $this->stringValue($row, 'from_path'),
            'to' => $this->stringValue($row, 'to_path'),
            'status' => $this->intValue($row, 'status'),
        ];
    }

    /** @param array<string, mixed> $row */
    private function stringValue(array $row, string $key): string
    {
        $value = $row[$key] ?? '';

        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * @param array<string, mixed> $row
     *
     * @noinspection PhpSameParameterValueInspection
     */
    private function intValue(array $row, string $key): int
    {
        $value = $row[$key] ?? 0;

        return is_numeric($value) ? (int) $value : 0;
    }
}
