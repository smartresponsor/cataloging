<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class CategoryVoter extends Voter
{
    public const OWNER = 'category.owner';
    public const EDITOR = 'category.editor';
    public const RULE = 'category.rule';
    public const MERCH = 'category.merch';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::OWNER, self::EDITOR, self::RULE, self::MERCH], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!is_object($user) || !method_exists($user, 'getRoles')) {
            $roles = method_exists($token, 'getRoleNames') ? $token->getRoleNames() : [];
        } else {
            $roles = $user->getRoles();
        }

        return match ($attribute) {
            self::OWNER => in_array('ROLE_CATEGORY_OWNER', $roles, true),
            self::EDITOR => in_array('ROLE_CATEGORY_EDITOR', $roles, true) || in_array('ROLE_CATEGORY_OWNER', $roles, true),
            self::RULE => in_array('ROLE_CATEGORY_RULE', $roles, true) || in_array('ROLE_CATEGORY_EDITOR', $roles, true) || in_array('ROLE_CATEGORY_OWNER', $roles, true),
            self::MERCH => in_array('ROLE_CATEGORY_MERCH', $roles, true) || in_array('ROLE_CATEGORY_EDITOR', $roles, true) || in_array('ROLE_CATEGORY_OWNER', $roles, true),
            default => false,
        };
    }
}
