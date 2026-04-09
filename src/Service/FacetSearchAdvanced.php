<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the facet search advanced application service.
 */
final class FacetSearchAdvanced
{
    private \PDO $pdo;
    private FacetFilter $filter;
    private FacetRank $rank;
    /**
     * Initializes the facet search advanced service collaborators.
     */
    public function __construct(\PDO $pdo, FacetFilter $filter, FacetRank $rank)
    {
        $this->pdo = $pdo;
        $this->filter = $filter;
        $this->rank = $rank;
    }

    /** @return list<array{id:mixed,slug:mixed,name:mixed,path:mixed,locale:mixed}> */
    public function search(
        string $term,
        string $locale = 'en',
        ?string $pathPrefix = null,
        int $limit = 20,
        int $offset = 0,
    ): array
    {
        $sql = 'SELECT id, slug, name, path, locale FROM category_projection WHERE (slug LIKE :q OR name LIKE :q)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':q', '%'.$term.'%');
        $stmt->execute();
        /** @var list<array{id:mixed,slug:mixed,name:mixed,path:mixed,locale:mixed}> $rows */
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        $rows = array_values(array_filter(
            $rows,
            fn (array $r): bool => $this->filter->scope($r, $pathPrefix, $locale),
        ));
        usort($rows, fn (array $a, array $b): int => $this->rank->score($term, $b) <=> $this->rank->score($term, $a));

        return array_slice($rows, $offset, $limit);
    }
}
