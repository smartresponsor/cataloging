<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\ValueObject\CategoryCatalogReadNodeRequest;
use App\Cataloging\ValueObject\CategoryCatalogReadPageRequest;

/**
 * Defines the contract for catalog read service.
 */
/** @noinspection PhpCSFixerValidationInspection */
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
