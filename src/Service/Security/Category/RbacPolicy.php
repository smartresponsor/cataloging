<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Security\Category;

final class RbacPolicy
{
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
