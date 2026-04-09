<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;
/**
 * Defines the contract for tenant role policy.
 */
interface TenantRolePolicyInterface
{
    /** @param array{org:string,tenant:string,role:string} $ctx */
    public function allow(array $ctx, string $action): bool;
}
