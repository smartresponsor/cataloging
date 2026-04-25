<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\ValueObject\CategoryProjectionCriteria;

/**
 * Defines the contract for category projection read service.
 */
interface CategoryProjectionReadServiceInterface
{
    /**
     * @return list<array<string,mixed>>
     */
    public function list(?CategoryProjectionCriteria $criteria = null): array;

    /**
     * @return list<array<string,mixed>>
     */
    public function tree(?CategoryProjectionCriteria $criteria = null): array;

    /**
     * @return array<string,mixed>|null
     */
    public function findOne(string $id): ?array;
}
