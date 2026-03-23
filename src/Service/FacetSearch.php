<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class FacetSearch
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function search(string $term, string $locale = 'en', int $limit = 20, int $offset = 0): array
    {
        $sql = 'SELECT id, slug, name, path, locale FROM category_projection 
                WHERE locale = :locale AND (slug LIKE :q OR name LIKE :q) 
                ORDER BY name ASC LIMIT :lim OFFSET :off';
        $stmt = $this->pdo->prepare($sql);
        $like = '%'.$term.'%';
        $stmt->bindValue(':locale', $locale);
        $stmt->bindValue(':q', $like);
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn (array $r) => [
            'id' => (string) $r['id'],
            'slug' => (string) $r['slug'],
            'name' => (string) $r['name'],
            'path' => (string) ($r['path'] ?? ''),
            'locale' => (string) $r['locale'],
        ], $rows ?: []);
    }
}
