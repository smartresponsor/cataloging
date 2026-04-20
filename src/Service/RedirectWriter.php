<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the redirect writer application service.
 */
final readonly class RedirectWriter
{
    /**
     * Initializes the redirect writer service collaborators.
     */
    public function __construct(private \PDO $database)
    {
    }

    /** @param list<array<string,mixed>> $rows */
    public function write(array $rows): int
    {
        $sql = 'INSERT INTO redirect_rule(from_path,to_path,locale,source) VALUES(:from,:to,:locale,\'category-move\')
               ON CONFLICT (locale, from_path) DO UPDATE SET to_path=EXCLUDED.to_path';
        $statement = $this->database->prepare($sql);
        $writtenCount = 0;
        foreach ($rows as $row) {
            $statement->execute([':from' => $row['from'], ':to' => $row['to'], ':locale' => $row['locale'] ?? null]);
            $writtenCount += $statement->rowCount();
        }

        return $writtenCount;
    }
}
