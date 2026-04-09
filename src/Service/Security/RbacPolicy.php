<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Security;
/**
 * Provides the rbac policy application service.
 */
final class RbacPolicy
{
    /**
     * Handles the allow workflow.
     */
    public function allow(string $role, string $action): bool
    {
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
