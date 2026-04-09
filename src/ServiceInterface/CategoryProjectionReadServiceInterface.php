<?php

declare(strict_types=1);

namespace App\ServiceInterface;

/**
 * Defines the contract for category projection read service.
 */
interface CategoryProjectionReadServiceInterface
{
    /**
     * @param array<string,mixed> $criteria
     *
     * @return array tenant':string,
     *               workflow_state:string,
     *               published:bool,
     *               published_at:?string,
     *               updated_at:string
     *               }>
     */
    public function list(array $criteria = []): array;

    /**
     * @param array<string,mixed> $criteria
     *
     * @return list<array<string,mixed>>
     */
    public function tree(array $criteria = []): array;

    /**
     * @param string $id
     *
     * @return array|null tenant':string,
     *                    workflow_state:string,
     *                    published:bool,
     *                    published_at:?string,
     *                    updated_at:string
     *                    }|null
     */
    public function findOne(string $id): ?array;
}
