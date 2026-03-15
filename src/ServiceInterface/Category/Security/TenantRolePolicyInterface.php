<?php

declare(strict_types=1);

namespace App\Layer\Security;

interface TenantRolePolicyInterface
{
    /** @param array{org:string,tenant:string,role:string} $ctx */
    public function allow(array $ctx, string $action): bool;
}
