<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Security;

use App\Entity\Category;
use App\RepositoryInterface\CategoryAccessAssignmentRepositoryInterface;
use App\Service\Security\CategoryRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/** @extends Voter<string, mixed> */
final class CategoryVoter extends Voter
{
    public function __construct(private readonly ?CategoryAccessAssignmentRepositoryInterface $accessAssignmentRepository = null)
    {
    }
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
        $grantedByRole = match ($attribute) {
            self::OWN => in_array(CategoryRole::OWNER, $roles, true),
            self::EDIT => in_array(CategoryRole::EDITOR, $roles, true) || in_array(CategoryRole::OWNER, $roles, true),
            self::PUBLISH => in_array(CategoryRole::PUBLISHER, $roles, true) || in_array(CategoryRole::OWNER, $roles, true),
            self::VIEW => true,
            default => false,
        };
        if ($grantedByRole) {
            return true;
        }
        if (!$subject instanceof Category || null === $this->accessAssignmentRepository) {
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
