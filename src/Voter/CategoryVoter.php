<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Voter;

use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryAccessAssignmentRepositoryInterface;
use App\Cataloging\ValueObject\Security\CategoryAuthorizationSubject;
use App\Cataloging\ValueObject\Security\CategoryRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/** @extends Voter<string, mixed> */
final class CategoryVoter extends Voter
{
    /**
     * Initializes the category voter service collaborators.
     */
    public function __construct(
        private readonly ?CatalogCategoryAccessAssignmentRepositoryInterface $accessAssignmentRepository = null,
    ) {
    }

    /** @noinspection PhpConstantNamingConventionInspection */
    public const string VIEW = 'category.view';
    /** @noinspection PhpConstantNamingConventionInspection */
    public const string EDIT = 'category.edit';
    /** @noinspection PhpConstantNamingConventionInspection */
    public const string OWN = 'category.own';
    public const string PUBLISH = 'category.publish';

    /**
     * Checks whether this service supports the provided input.
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::OWN, self::PUBLISH], true);
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
        $roles = $token->getRoleNames();
        if (in_array('ROLE_SUPER_ADMIN', $roles, true) || in_array('ROLE_ADMIN', $roles, true)) {
            return true;
        }
        $grantedByRole = match ($attribute) {
            self::OWN => in_array(CategoryRole::OWNER, $roles, true),
            self::EDIT => in_array(CategoryRole::EDITOR, $roles, true) || in_array(CategoryRole::OWNER, $roles, true),
            self::PUBLISH => in_array(CategoryRole::PUBLISHER, $roles, true)
                || in_array(CategoryRole::OWNER, $roles, true),
            self::VIEW => true,
            default => false,
        };
        if ($grantedByRole) {
            return true;
        }
        if (!$subject instanceof CategoryAuthorizationSubject || null === $this->accessAssignmentRepository) {
            return self::VIEW === $attribute;
        }
        $user = $token->getUser();
        $actorUserId = $user instanceof UserInterface ? trim($user->getUserIdentifier()) : null;
        if (null === $actorUserId || '' === $actorUserId) {
            return self::VIEW === $attribute;
        }
        $assignment = $this->accessAssignmentRepository->findOneByCategoryIdAndActorUserId($subject->id, $actorUserId);
        if (null === $assignment || 'active' !== $assignment->status()) {
            return self::VIEW === $attribute;
        }

        return match ($attribute) {
            self::OWN => 'owner' === $assignment->role(),
            self::EDIT => in_array($assignment->role(), ['owner', 'editor'], true),
            self::PUBLISH => in_array($assignment->role(), ['owner', 'publisher'], true),
            self::VIEW => true,
            default => false,
        };
    }
}
