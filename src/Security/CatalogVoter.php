<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/** @extends Voter<string, mixed> */
final class CatalogVoter extends Voter
{
    public const string OWNER = 'category.owner';
    public const string EDITOR = 'category.editor';

    /** @noinspection PhpConstantNamingConventionInspection */
    public const string RULE = 'category.rule';

    public const string MERCH = 'category.merch';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::OWNER, self::EDITOR, self::RULE, self::MERCH], true);
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null,
    ): bool {
        $user = $token->getUser();
        /** @var list<string> $roles */
        $roles = $user instanceof UserInterface ? $user->getRoles() : $token->getRoleNames();

        return match ($attribute) {
            self::OWNER => in_array('ROLE_CATEGORY_OWNER', $roles, true),
            self::EDITOR => in_array('ROLE_CATEGORY_EDITOR', $roles, true)
                || in_array('ROLE_CATEGORY_OWNER', $roles, true),
            self::RULE => in_array('ROLE_CATEGORY_RULE', $roles, true)
                || in_array('ROLE_CATEGORY_EDITOR', $roles, true)
                || in_array('ROLE_CATEGORY_OWNER', $roles, true),
            self::MERCH => in_array('ROLE_CATEGORY_MERCH', $roles, true)
                || in_array('ROLE_CATEGORY_EDITOR', $roles, true)
                || in_array('ROLE_CATEGORY_OWNER', $roles, true),
            default => false,
        };
    }
}
