<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service\Security;

/**
 * Provides the rbac policy application service.
 */
final readonly class RbacPolicy
{
    /**
     * Handles the allow workflow.
     */
    public function allow(string $role, string $action): bool
    {
        return match ($role) {
            CategoryRole::OWNER => true,
            CategoryRole::PUBLISHER => in_array($action, ['publish', 'read'], true),
            CategoryRole::EDITOR => in_array($action, ['edit', 'read'], true),
            CategoryRole::READER => 'read' === $action,
            CategoryRole::AUDITOR => in_array($action, ['read', 'audit'], true),
            default => false,
        };
    }
}
