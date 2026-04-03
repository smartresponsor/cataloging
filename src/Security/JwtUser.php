<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

final readonly class JwtUser implements UserInterface
{
    /**
     * @param non-empty-string $identifier
     * @param list<string> $roles
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

    public function eraseCredentials(): void
    {
    }

    public function getPassword(): ?string
    {
        return null;
    }

    /** @return non-empty-string */
    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }
}
