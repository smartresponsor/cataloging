<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\User;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provides the jwt user implementation.
 */
final readonly class JwtUser implements UserInterface
{
    /**
     * @param non-empty-string $identifier
     * @param list<string>     $roles
     */
    public function __construct(
        private string $identifier,
        private array $roles,
    ) {
    }

    /** @return list<string> */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Handles the erase credentials workflow.
     */
    public function eraseCredentials(): void
    {
    }

    /** @return non-empty-string */
    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }
}
