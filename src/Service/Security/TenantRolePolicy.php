<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Security;

use App\ServiceInterface\Security\TenantRolePolicyInterface;

final class TenantRolePolicy implements TenantRolePolicyInterface
{
    /**
     * @param array{org:string,tenant:string,role:string} $ctx
     */
    public function allow(array $ctx, string $action): bool
    {
        $role = $ctx['role'] ?? '';
        if (CatalogCategoryRole::OWNER === $role) {
            return true;
        }
        if (CatalogCategoryRole::PUBLISHER === $role) {
            return in_array($action, ['publish', 'read'], true);
        }
        if (CatalogCategoryRole::EDITOR === $role) {
            return in_array($action, ['edit', 'read'], true);
        }
        if (CatalogCategoryRole::READER === $role) {
            return 'read' === $action;
        }
        if (CatalogCategoryRole::AUDITOR === $role) {
            return in_array($action, ['read', 'audit'], true);
        }

        return false;
    }
}
