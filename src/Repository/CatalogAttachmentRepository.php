<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Repository;

use App\Cataloging\Entity\CatalogCategoryAttachmentEntity;
use App\Cataloging\RepositoryInterface\CatalogAttachmentRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides repository services for catalog attachment repository.
 */
final class CatalogAttachmentRepository implements CatalogAttachmentRepositoryInterface
{
    public function __construct(private readonly ?EntityManagerInterface $entityManager = null)
    {
    }

    public function list(?string $categoryId = null): array
    {
        if (null === $this->entityManager) {
            return [];
        }

        $repository = $this->entityManager->getRepository(CatalogCategoryAttachmentEntity::class);
        $entities = null !== $categoryId && '' !== $categoryId
            ? $repository->findBy(['categoryId' => $categoryId], ['createdAt' => 'DESC', 'attachmentId' => 'DESC'])
            : $repository->findBy([], ['createdAt' => 'DESC', 'attachmentId' => 'DESC']);

        return array_values(array_map(
            fn (CatalogCategoryAttachmentEntity $entity): array => $this->normalizeAttachmentEntity($entity),
            array_values(array_filter($entities, fn ($entity): bool => $entity instanceof CatalogCategoryAttachmentEntity)),
        ));
    }

    public function add(
        string $categoryId,
        string $type,
        string $provider,
        string $externalAttachmentId,
        ?string $referenceUri = null,
    ): array {
        if (null === $this->entityManager) {
            throw new \RuntimeException('Doctrine entity manager is required for attachment storage.');
        }

        $existing = $this->entityManager->getRepository(CatalogCategoryAttachmentEntity::class)->findOneBy([
            'categoryId' => $categoryId,
            'type' => $type,
            'provider' => $provider,
            'externalAttachmentId' => $externalAttachmentId,
        ]);
        if ($existing instanceof CatalogCategoryAttachmentEntity) {
            return $this->normalizeAttachmentEntity($existing);
        }

        $entity = new CatalogCategoryAttachmentEntity($categoryId, $type, $provider, $externalAttachmentId, $referenceUri);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->normalizeAttachmentEntity($entity);
    }

    public function findOne(string $attachmentId): ?array
    {
        if (null === $this->entityManager) {
            return null;
        }

        $entity = $this->entityManager->find(CatalogCategoryAttachmentEntity::class, $attachmentId);

        return $entity instanceof CatalogCategoryAttachmentEntity ? $this->normalizeAttachmentEntity($entity) : null;
    }

    public function delete(string $attachmentId): bool
    {
        if (null === $this->entityManager) {
            return false;
        }

        $entity = $this->entityManager->find(CatalogCategoryAttachmentEntity::class, $attachmentId);
        if (!$entity instanceof CatalogCategoryAttachmentEntity) {
            return false;
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @return array{attachment_id:string,category_id:string,type:string,provider:string,external_attachment_id:string,reference_uri:string|null,path:string|null,created_at:string}
     */
    private function normalizeAttachmentEntity(CatalogCategoryAttachmentEntity $entity): array
    {
        return [
            'attachment_id' => $entity->getAttachmentId(),
            'category_id' => $entity->getCategoryId(),
            'type' => $entity->getType(),
            'provider' => $entity->getProvider(),
            'external_attachment_id' => $entity->getExternalAttachmentId(),
            'reference_uri' => $entity->getReferenceUri(),
            'path' => $entity->getReferenceUri(),
            'created_at' => $entity->getCreatedAt()->format(DATE_ATOM),
        ];
    }
}
