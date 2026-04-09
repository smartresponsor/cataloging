<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the redirect writer application service.
 */
final class RedirectWriter
{
    private \PDO $db;
    /**
     * Initializes the redirect writer service collaborators.
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /** @param array<int,array{from:string,to:string,locale:?string}> $rows */
    public function write(array $rows): int
    {
        $sql = 'INSERT INTO redirect_rule(from_path,to_path,locale,source) VALUES(:from,:to,:locale,\'category-move\')
               ON CONFLICT (locale, from_path) DO UPDATE SET to_path=EXCLUDED.to_path';
        $stmt = $this->db->prepare($sql);
        $n = 0;
        foreach ($rows as $r) {
            $stmt->execute([':from' => $r['from'], ':to' => $r['to'], ':locale' => $r['locale'] ?? null]);
            $n += (int) $stmt->rowCount();
        }

        return $n;
    }
}
