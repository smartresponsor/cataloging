<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class CategoryVoter extends Voter
{
    public const VIEW = 'category.view';
    public const EDIT = 'category.edit';
    public const OWN = 'category.own';
    public const PUBLISH = 'category.publish';

    public function vote(TokenInterface $token, mixed $subject, array $attributes, ?Vote $vote = null): int
    {
        $roles = $token->getRoleNames();
        $user = $token->getUser();

        if ([] === $roles && $user instanceof UserInterface) {
            $roles = $user->getRoles();
        }

        foreach ($attributes as $attribute) {
            if (!\is_string($attribute)) {
                continue;
            }

            $normalized = $this->normalizeAttribute($attribute);

            if (null === $normalized) {
                continue;
            }

            if (\in_array('ROLE_SUPER_ADMIN', $roles, true) || \in_array('ROLE_ADMIN', $roles, true)) {
                return VoterInterface::ACCESS_GRANTED;
            }

            return $this->voteOnAttribute($normalized, $subject, $token, $vote)
                ? VoterInterface::ACCESS_GRANTED
                : VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }

    public function supportsAttribute(string $attribute): bool
    {
        return null !== $this->normalizeAttribute($attribute);
    }

    public function supportsType(string $subjectType): bool
    {
        return true;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $this->supportsAttribute($attribute);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $roles = $token->getRoleNames();
        $user = $token->getUser();

        if ([] === $roles && $user instanceof UserInterface) {
            $roles = $user->getRoles();
        }

        return match ($attribute) {
            self::OWN => \in_array('category.owner', $roles, true),
            self::EDIT => \in_array('category.editor', $roles, true) || \in_array('category.owner', $roles, true),
            self::PUBLISH => \in_array('category.publisher', $roles, true) || \in_array('category.owner', $roles, true),
            self::VIEW => true,
            default => false,
        };
    }

    private function normalizeAttribute(string $attribute): ?string
    {
        return match ($attribute) {
            self::VIEW, 'VIEW', 'view' => self::VIEW,
            self::EDIT, 'EDIT', 'edit' => self::EDIT,
            self::OWN, 'OWN', 'own' => self::OWN,
            self::PUBLISH, 'PUBLISH', 'publish' => self::PUBLISH,
            default => null,
        };
    }
}
