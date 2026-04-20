<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Category;
use App\Cataloging\RepositoryInterface\CatalogAttachmentRepositoryInterface;
use App\Cataloging\Security\CategoryVoter;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides the category attachment authorization service application service.
 */
final readonly class CategoryAttachmentAuthorizationService
{
    /**
     * Initializes the category attachment authorization service service collaborators.
     */
    public function __construct(
        private Security $security,
        private CatalogAttachmentRepositoryInterface $attachmentRepository,
        private CategoryTenantAccessEvaluator $tenantAccessEvaluator,
    ) {
    }

    /**
     * Handles the assert can list workflow.
     *
     * @throws Exception
     */
    public function assertCanList(?string $categoryId): void
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $normalizedCategoryId = $this->normalizeCategoryId($categoryId);

        if (null === $normalizedCategoryId) {
            throw new AccessDeniedHttpException('Listing attachments without category scope is not allowed for the current actor.');
        }

        $this->assertGranted(
            CategoryVoter::VIEW,
            $normalizedCategoryId,
            'Category attachment listing is not allowed for the current actor.',
        );
    }

    /**
     * Handles the assert can attach workflow.
     *
     * @throws Exception
     */
    public function assertCanAttach(string $categoryId): void
    {
        $this->assertGranted(
            CategoryVoter::EDIT,
            $categoryId,
            'Category attachment binding is not allowed for the current actor.',
        );
    }

    /**
     * Handles the assert can detach workflow.
     *
     * @throws Exception
     */
    public function assertCanDetach(string $attachmentId): void
    {
        $attachment = $this->attachmentRepository->findOne(trim($attachmentId));
        if (null === $attachment) {
            throw new AccessDeniedHttpException('Category attachment deletion target could not be resolved.');
        }

        $this->assertGranted(
            CategoryVoter::EDIT,
            $attachment['category_id'],
            'Category attachment deletion is not allowed for the current actor.',
        );
    }

    private function normalizeCategoryId(?string $categoryId): ?string
    {
        if (null === $categoryId) {
            return null;
        }

        $normalizedCategoryId = trim($categoryId);

        return '' !== $normalizedCategoryId ? $normalizedCategoryId : null;
    }

    /**
     * @throws Exception
     */
    private function assertGranted(string $attribute, string $categoryId, string $message): void
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $categoryTenant = $this->tenantAccessEvaluator->categoryTenant($categoryId);
        $externalIdentityContext = $this->tenantAccessEvaluator->resolveExternalIdentityContext();
        if (null === $externalIdentityContext || null === $externalIdentityContext->tenant) {
            throw new AccessDeniedHttpException('External tenant identity is required for category attachment operations.');
        }
        if (null !== $categoryTenant && $externalIdentityContext->tenant !== $categoryTenant) {
            throw new AccessDeniedHttpException('Cross-tenant category attachment operation is not allowed for the current actor.');
        }

        $subject = new Category();
        $subject->id = trim($categoryId);

        if ($this->security->isGranted($attribute, $subject)) {
            return;
        }

        if ($this->tenantAccessEvaluator->externalTenantRoleAllows(
            $attribute,
            $externalIdentityContext->tenant,
            $externalIdentityContext->categoryRoles,
            $categoryTenant,
        )) {
            return;
        }

        throw new AccessDeniedHttpException($message);
    }
}
