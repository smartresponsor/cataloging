<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class CategoryVoter extends Voter
{
    public const VIEW = 'category.view';
    public const EDIT = 'category.edit';
    public const OWN = 'category.own';
    public const PUBLISH = 'category.publish';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::OWN, self::PUBLISH], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $roles = $token->getRoleNames();

        if (in_array('ROLE_SUPER_ADMIN', $roles, true) || in_array('ROLE_ADMIN', $roles, true)) {
            return true;
        }

        return match ($attribute) {
            self::OWN => in_array('category.owner', $roles, true),
            self::EDIT => in_array('category.editor', $roles, true) || in_array('category.owner', $roles, true),
            self::PUBLISH => in_array('category.publisher', $roles, true) || in_array('category.owner', $roles, true),
            self::VIEW => true,
            default => false,
        };
    }
}
