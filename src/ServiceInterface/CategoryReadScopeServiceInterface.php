<?php

declare(strict_types=1);

namespace App\ServiceInterface;

use Symfony\Component\HttpFoundation\Request;

interface CategoryReadScopeServiceInterface
{
    /**
     * @param array<string,mixed> $criteria
     * @return array<string,mixed>
     */
    public function applyTenantScope(Request $request, array $criteria): array;
}
