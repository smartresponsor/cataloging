<?php

declare(strict_types=1);

namespace App\ServiceInterface;

use App\ValueObject\CategoryProjectionCriteria;
use Doctrine\DBAL\Exception;

/**
 * Defines the contract for category projection read service.
 */
interface CategoryProjectionReadServiceInterface
{
    /**
     * @return list<array<string,mixed>>
     *
     * @throws Exception
     */
    public function list(?CategoryProjectionCriteria $criteria = null): array;

    /**
     * @return list<array<string,mixed>>
     *
     * @throws Exception
     */
    public function tree(?CategoryProjectionCriteria $criteria = null): array;

    /**
     * @return array<string,mixed>|null
     *
     * @throws Exception
     */
    public function findOne(string $id): ?array;
}
