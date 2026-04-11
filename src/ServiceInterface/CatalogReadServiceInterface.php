<?php

declare(strict_types=1);

namespace App\ServiceInterface;

use App\ValueObject\CategoryCatalogReadNodeRequest;
use App\ValueObject\CategoryCatalogReadPageRequest;

/**
 * Defines the contract for catalog read service.
 */
interface CatalogReadServiceInterface
{
    /** @return array{id:string,name:string,slug:string,path:string,depth:int}|null */
    public function byId(CategoryCatalogReadNodeRequest $request): ?array;

    /** @return list<array{id:string,name:string,slug:string,path:string,depth:int}>|null */
    public function childList(CategoryCatalogReadNodeRequest $request): ?array;

    /** @return list<array{id:string,name:string,slug:string,path:string,depth:int}>|null */
    public function ancestorList(CategoryCatalogReadNodeRequest $request): ?array;

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
    public function descendantsTree(CategoryCatalogReadNodeRequest $request): ?array;

    /** @return array{item:list<array{id:string,name:string,slug:string,path:string,depth:int}>,after:string} */
    public function list(CategoryCatalogReadPageRequest $request): array;
}
