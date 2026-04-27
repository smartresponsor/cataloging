<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Service\Security\CategoryRole;
use App\Cataloging\ServiceInterface\CatalogTenantRolePolicyServiceInterface;

/**
 * Provides the tenant role policy application service.
 */
final class CatalogTenantRolePolicyService implements CatalogTenantRolePolicyServiceInterface
{
    /**
     * @param array{org:string,tenant:string,role:string} $ctx
     */
    public function allow(array $ctx, string $action): bool
    {
        $role = $ctx['role'];
        if (CategoryRole::OWNER === $role) {
            return true;
        }
        if (CategoryRole::PUBLISHER === $role) {
            return in_array($action, ['publish', 'read'], true);
        }
        if (CategoryRole::EDITOR === $role) {
            return in_array($action, ['edit', 'read'], true);
        }
        if (CategoryRole::READER === $role) {
            return 'read' === $action;
        }
        if (CategoryRole::AUDITOR === $role) {
            return in_array($action, ['read', 'audit'], true);
        }

        return false;
    }
}
