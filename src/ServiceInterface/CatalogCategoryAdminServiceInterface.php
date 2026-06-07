<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

interface CatalogCategoryAdminServiceInterface
{
    /**
     * @return array{item:list<array{id:int,slug:string,name:string,locale:string,status:string}>, nextCursor:?string}
     */
    public function list(string $query, ?string $cursor, int $limit): array;

    /**
     * @return array<string, mixed>
     */
    public function read(string $id): array;

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public function save(string $id, array $payload): array;
}
