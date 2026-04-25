<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

/**
 * Represents a durable category attachment boundary record.
 */
#[ORM\Entity]
#[ORM\Table(name: 'category_attachment')]
#[ORM\Index(name: 'idx_category_attachment_category', columns: ['category_id'])]
class CatalogCategoryAttachmentEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 26, name: 'attachment_id')]
    private string $attachmentId;

    #[ORM\Column(type: 'string', length: 26, name: 'category_id')]
    private string $categoryId;

    #[ORM\Column(type: 'string', length: 64)]
    private string $type;

    #[ORM\Column(type: 'string', length: 64)]
    private string $provider;

    #[ORM\Column(type: 'string', length: 255, name: 'external_attachment_id')]
    private string $externalAttachmentId;

    #[ORM\Column(type: 'string', length: 2048, nullable: true, name: 'path')]
    private ?string $referenceUri;

    #[ORM\Column(type: 'datetime_immutable', name: 'created_at')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $categoryId,
        string $type,
        string $provider,
        string $externalAttachmentId,
        ?string $referenceUri = null,
    ) {
        $this->attachmentId = (string) new Ulid();
        $this->categoryId = $categoryId;
        $this->type = $type;
        $this->provider = $provider;
        $this->externalAttachmentId = $externalAttachmentId;
        $this->referenceUri = null !== $referenceUri && '' !== trim($referenceUri) ? trim($referenceUri) : null;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getAttachmentId(): string
    {
        return $this->attachmentId;
    }

    public function getCategoryId(): string
    {
        return $this->categoryId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getExternalAttachmentId(): string
    {
        return $this->externalAttachmentId;
    }

    public function getReferenceUri(): ?string
    {
        return $this->referenceUri;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
