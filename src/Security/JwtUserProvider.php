<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/** @implements UserProviderInterface<UserInterface> */
final class JwtUserProvider implements UserProviderInterface
{
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return new class($this->normalizeIdentifier($identifier)) implements UserInterface {
            /** @param non-empty-string $username */
            public function __construct(private readonly string $username)
            {
            }

            /** @return list<string> */
            public function getRoles(): array
            {
                return ['ROLE_ADMIN'];
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
                return $this->username;
            }
        };
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return true;
    }

    /** @return non-empty-string */
    private function normalizeIdentifier(string $identifier): string
    {
        $normalized = trim($identifier);

        return '' !== $normalized ? $normalized : 'anonymous';
    }
}
