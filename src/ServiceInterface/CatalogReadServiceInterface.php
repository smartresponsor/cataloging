<?php

declare(strict_types=1);

namespace App\ServiceInterface;
/**
 * Defines the contract for catalog read service.
 */
interface CatalogReadServiceInterface
{
    /** @return array{id:string,name:string,slug:string,path:string,depth:int}|null */
    public function byId(string $id): ?array;

    /** @return list<array{id:string,name:string,slug:string,path:string,depth:int}>|null */
    public function childList(string $id): ?array;

    /** @return list<array{id:string,name:string,slug:string,path:string,depth:int}>|null */
    public function ancestorList(string $id): ?array;

    /**
 * @return array{
 *     id:string,
 *     name:string,
 *     slug:string,
 *     path:string,
 *     depth:int,
 *     children:list<array{
 *         id:string,
 *         name:string,
 *         slug:string,
 *         path:string,
 *         depth:int,
 *     }>,
 * }|null
 */
    public function descendantsTree(string $id): ?array;

    /** @return array{item:list<array{id:string,name:string,slug:string,path:string,depth:int}>,after:string} */
    public function list(int $first, string $after): array;
}
