<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class FacetSearchAdvanced
{
    private \PDO $pdo;
    private FacetFilter $filter;
    private FacetRank $rank;

    public function __construct(\PDO $pdo, FacetFilter $filter, FacetRank $rank)
    {
        $this->pdo = $pdo;
        $this->filter = $filter;
        $this->rank = $rank;
    }

    public function search(string $term, string $locale = 'en', ?string $pathPrefix = null, int $limit = 20, int $offset = 0): array
    {
        $sql = 'SELECT id, slug, name, path, locale FROM category_projection WHERE (slug LIKE :q OR name LIKE :q)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':q', '%'.$term.'%');
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        $rows = array_values(array_filter($rows, fn ($r) => $this->filter->scope($r, $pathPrefix, $locale)));
        usort($rows, fn ($a, $b) => $this->rank->score($term, $b) <=> $this->rank->score($term, $a));

        return array_slice($rows, $offset, $limit);
    }
}
