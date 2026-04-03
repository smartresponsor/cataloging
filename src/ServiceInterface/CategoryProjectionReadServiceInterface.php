<?php

declare(strict_types=1);

namespace App\ServiceInterface;

interface CategoryProjectionReadServiceInterface
{
    /**
     * @param array<string,mixed> $criteria
     * @return list<array{
     *   id:string,
     *   slug:string,
     *   name:string,
     *   parent_id:?string,
     *   path:string,
     *   locale:string,
     *   tenant:string,
     *   workflow_state:string,
     *   published:bool,
     *   published_at:?string,
     *   updated_at:string
     * }>
     */
    public function list(array $criteria = []): array;

    /**
     * @param array<string,mixed> $criteria
     * @return list<array<string,mixed>>
     */
    public function tree(array $criteria = []): array;

    /**
     * @return array{
     *   id:string,
     *   slug:string,
     *   name:string,
     *   parent_id:?string,
     *   path:string,
     *   locale:string,
     *   tenant:string,
     *   workflow_state:string,
     *   published:bool,
     *   published_at:?string,
     *   updated_at:string
     * }|null
     */
    public function findOne(string $id): ?array;
}
