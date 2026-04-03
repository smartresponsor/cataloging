<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\AttachmentInterface\AttachmentReferenceGatewayInterface;
use App\RepositoryInterface\CatalogAttachmentRepositoryInterface;

final class AttachmentService
{
    public function __construct(
        private readonly CatalogAttachmentRepositoryInterface $repository,
        private readonly AttachmentReferenceGatewayInterface $gateway,
    ) {
    }

    /**
     * @return list<array{attachment_id:string,category_id:string,type:string,provider:string,external_attachment_id:string,reference_uri:?string,path:?string,created_at:string}>
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
     * @return array{attachment_id:string,category_id:string,type:string,provider:string,external_attachment_id:string,reference_uri:?string,path:?string,created_at:string}
     */
    public function add(string $categoryId, string $type, string $provider, string $externalAttachmentId, ?string $referenceUri = null): array
    {
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

    public function remove(string $attachmentId): bool
    {
        $normalizedAttachmentId = trim($attachmentId);
        if ('' === $normalizedAttachmentId) {
            throw new \InvalidArgumentException('attachment_id is required');
        }

        return $this->repository->delete($normalizedAttachmentId);
    }
}
