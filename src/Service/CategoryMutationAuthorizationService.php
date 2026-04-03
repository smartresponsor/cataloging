<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Security\CategoryVoter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final readonly class CategoryMutationAuthorizationService
{
    public function __construct(private Security $security)
    {
    }

    public function assertCanMove(string $categoryId): void
    {
        $this->assertGranted(CategoryVoter::EDIT, $categoryId, 'Category move is not allowed for the current actor.');
    }

    public function assertCanPublish(string $categoryId): void
    {
        $this->assertGranted(CategoryVoter::PUBLISH, $categoryId, 'Category publish is not allowed for the current actor.');
    }

    private function assertGranted(string $attribute, string $categoryId, string $message): void
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $subject = new Category();
        $subject->id = trim($categoryId);

        if ($this->security->isGranted($attribute, $subject)) {
            return;
        }

        throw new AccessDeniedHttpException($message);
    }
}
