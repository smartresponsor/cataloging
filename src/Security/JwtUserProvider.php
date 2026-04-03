<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/** @implements UserProviderInterface<JwtUser> */
final class JwtUserProvider implements UserProviderInterface
{
    /**
     * @param list<string> $defaultRoles
     * @param list<string> $adminIdentifiers
     */
    public function __construct(
        private readonly array $defaultRoles = ['ROLE_USER'],
        private readonly array $adminIdentifiers = [],
    ) {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $normalizedIdentifier = $this->normalizeIdentifier($identifier);

        return new JwtUser($normalizedIdentifier, $this->resolveRoles($normalizedIdentifier));
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof JwtUser) {
            throw new \InvalidArgumentException(sprintf('Unsupported user instance "%s".', $user::class));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return JwtUser::class === $class;
    }

    /** @return non-empty-string */
    private function normalizeIdentifier(string $identifier): string
    {
        $normalized = trim($identifier);

        return '' !== $normalized ? $normalized : 'anonymous';
    }

    /** @return list<string> */
    private function resolveRoles(string $identifier): array
    {
        if (in_array($identifier, $this->adminIdentifiers, true)) {
            return ['ROLE_ADMIN'];
        }

        $roles = [];

        foreach ($this->defaultRoles as $role) {
            $normalizedRole = trim($role);
            if ('' === $normalizedRole || !str_starts_with($normalizedRole, 'ROLE_')) {
                continue;
            }
            $roles[] = $normalizedRole;
        }

        if ([] === $roles) {
            $roles[] = 'ROLE_USER';
        }

        return array_values(array_unique($roles));
    }
}
