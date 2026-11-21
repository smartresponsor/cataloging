<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a durable idempotency reservation for category mutations.
 */
#[ORM\Entity]
#[ORM\Table(name: 'category_idempotency')]
class CatalogCategoryIdempotencyEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 190, name: 'idempotency_key')]
    private string $idempotencyKey;

    #[ORM\Column(type: 'string', length: 64)]
    private string $operation;

    #[ORM\Column(type: 'string', length: 128, name: 'request_hash')]
    private string $requestHash;

    #[ORM\Column(type: 'datetime_immutable', name: 'created_at')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', name: 'expires_at')]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column(type: 'string', length: 190, nullable: true, name: 'correlation_id')]
    private ?string $correlationId;

    public function __construct(
        string $idempotencyKey,
        string $operation,
        string $requestHash,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $expiresAt,
        ?string $correlationId = null,
    ) {
        $this->idempotencyKey = $idempotencyKey;
        $this->operation = $operation;
        $this->requestHash = $requestHash;
        $this->createdAt = $createdAt;
        $this->expiresAt = $expiresAt;
        $this->correlationId = $correlationId;
    }

    public function getIdempotencyKey(): string
    {
        return $this->idempotencyKey;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getRequestHash(): string
    {
        return $this->requestHash;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function getCorrelationId(): ?string
    {
        return $this->correlationId;
    }

    public function isExpiredAt(\DateTimeImmutable $moment): bool
    {
        return $this->expiresAt <= $moment;
    }
}
