<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\AttachmentInterface\AttachmentReferenceGatewayInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogAttachmentRepositoryInterface;

/**
 * Provides the attachment service application service.
 */
final readonly class CatalogAttachmentService
{
    /**
     * Initializes the attachment service service collaborators.
     */
    public function __construct(
        private CatalogAttachmentRepositoryInterface $repository,
        private AttachmentReferenceGatewayInterface $gateway,
    ) {
    }

    /**
     * @param string|null $categoryId
     *
     * @return list<array<string,mixed>>
     */
    public function list(?string $categoryId = null): array
    {
        $normalizedCategoryId = null;
        if (is_string($categoryId)) {
            $trimmedCategoryId = trim($categoryId);
            if ('' !== $trimmedCategoryId) {
                $normalizedCategoryId = $trimmedCategoryId;
            }
        }

        return $this->repository->list($normalizedCategoryId);
    }

    /**
     * @return array<string,mixed>
     *
     * @throws \InvalidArgumentException
     */
    public function add(
        string $categoryId,
        string $type,
        string $provider,
        string $externalAttachmentId,
        ?string $referenceUri = null,
    ): array {
        $normalizedCategoryId = trim($categoryId);
        $normalizedType = trim($type);
        $normalizedProvider = trim($provider);
        $normalizedExternalAttachmentId = trim($externalAttachmentId);
        $normalizedReferenceUri = null === $referenceUri ? null : trim($referenceUri);
        if ('' === $normalizedCategoryId) {
            throw new \InvalidArgumentException('category_id is required');
        }
        if ('' === $normalizedType) {
            throw new \InvalidArgumentException('type is required');
        }
        $this->gateway->assertBindable($normalizedProvider, $normalizedExternalAttachmentId, $normalizedReferenceUri);

        return $this->repository->add(
            $normalizedCategoryId,
            $normalizedType,
            $normalizedProvider,
            $normalizedExternalAttachmentId,
            null !== $normalizedReferenceUri && '' !== $normalizedReferenceUri ? $normalizedReferenceUri : null,
        );
    }

    /**
     * Handles the remove workflow.
     *
     * @throws \InvalidArgumentException
     */
    public function remove(string $attachmentId): bool
    {
        $normalizedAttachmentId = trim($attachmentId);
        if ('' === $normalizedAttachmentId) {
            throw new \InvalidArgumentException('attachment_id is required');
        }

        return $this->repository->delete($normalizedAttachmentId);
    }
}
