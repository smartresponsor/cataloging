<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class TenantRolePolicy implements TenantRolePolicyInterface
{
    /**
     * @param array{org:string,tenant:string,role:string} $ctx
     */
    public function allow(array $ctx, string $action): bool
    {
        $role = $ctx['role'] ?? '';
        if (testsRole::OWNER === $role) {
            return true;
        }
        if (testsRole::PUBLISHER === $role) {
            return in_array($action, ['publish', 'read'], true);
        }
        if (testsRole::EDITOR === $role) {
            return in_array($action, ['edit', 'read'], true);
        }
        if (testsRole::READER === $role) {
            return 'read' === $action;
        }
        if (testsRole::AUDITOR === $role) {
            return in_array($action, ['read', 'audit'], true);
        }

        return false;
    }
}
