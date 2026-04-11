<?php

declare(strict_types=1);

namespace App\ServiceInterface;

use App\ValueObject\CategoryProjectionCriteria;

/**
 * Defines the contract for category projection read service.
 */
interface CategoryProjectionReadServiceInterface
{
    /**
     * @return list<array{
     *     id:string,
     *     slug:string,
     *     name:string,
     *     parent_id:?string,
     *     path:string,
     *     locale:string,
     *     tenant:string,
     *     workflow_state:string,
     *     published:bool,
     *     published_at:?string,
     *     updated_at:string
     * }>
     */
    public function list(?CategoryProjectionCriteria $criteria = null): array;

    /**
     * @return list<array<string,mixed>>
     */
    public function tree(?CategoryProjectionCriteria $criteria = null): array;

    /**
     * @return array{
     *     id:string,
     *     slug:string,
     *     name:string,
     *     parent_id:?string,
     *     path:string,
     *     locale:string,
     *     tenant:string,
     *     workflow_state:string,
     *     published:bool,
     *     published_at:?string,
     *     updated_at:string
     * }|null
     */
    public function findOne(string $id): ?array;
}
