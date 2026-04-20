<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the facet search application service.
 */
final class FacetSearch
{
    private \PDO $pdo;

    /**
     * Initializes the facet search service collaborators.
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @return list<array{id:string,slug:string,name:string,path:string,locale:string}> */
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
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $result = [];
        foreach ($rows as $row) {
            $id = $row['id'] ?? null;
            $slug = $row['slug'] ?? null;
            $name = $row['name'] ?? null;
            $path = $row['path'] ?? '';
            $rowLocale = $row['locale'] ?? null;

            $result[] = [
                'id' => is_scalar($id) ? (string) $id : '',
                'slug' => is_scalar($slug) ? (string) $slug : '',
                'name' => is_scalar($name) ? (string) $name : '',
                'path' => is_scalar($path) ? (string) $path : '',
                'locale' => is_scalar($rowLocale) ? (string) $rowLocale : '',
            ];
        }

        return $result;
    }
}
