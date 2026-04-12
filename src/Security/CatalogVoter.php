<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/** @extends Voter<string, mixed> */
final class CatalogVoter extends Voter
{
    public const string OWNER = 'category.owner';
    public const string EDITOR = 'category.editor';

    /** @noinspection PhpConstantNamingConventionInspection */
    public const string RULE = 'category.rule';

    public const string MERCH = 'category.merch';

    /**
     * Checks whether this service supports the provided input.
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::OWNER, self::EDITOR, self::RULE, self::MERCH], true);
    }

    /**
     * Handles the vote on attribute workflow.
     */
    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null,
    ): bool {
        $user = $token->getUser();
        if (is_object($user) && method_exists($user, 'getRoles')) {
            /** @var list<string> $roles */
            $roles = $user->getRoles();
        } else {
            /** @var list<string> $roles */
            $roles = method_exists($token, 'getRoleNames') ? $token->getRoleNames() : [];
        }

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
