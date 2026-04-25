<?php

declare(strict_types=1);

namespace App\Cataloging\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'outbox')]
#[ORM\Index(name: 'idx_outbox_projection_ready', columns: ['processed_at', 'dead_lettered_at', 'available_at', 'created_at'])]
class CatalogOutboxMessageEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 120)]
    private string $type;

    #[ORM\Column(type: 'text')]
    private string $payload;

    #[ORM\Column(name: 'key', type: 'string', length: 190, unique: true)]
    private string $messageKey;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'available_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $availableAt = null;

    #[ORM\Column(type: 'integer')]
    private int $attempts = 0;

    #[ORM\Column(name: 'last_error', type: 'text', nullable: true)]
    private ?string $lastError = null;

    #[ORM\Column(name: 'dispatched_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dispatchedAt = null;

    #[ORM\Column(name: 'processed_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $processedAt = null;

    #[ORM\Column(name: 'dead_lettered_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deadLetteredAt = null;

    public function __construct(string $id, string $type, string $payload, string $messageKey, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->type = $type;
        $this->payload = $payload;
        $this->messageKey = $messageKey;
        $this->createdAt = $createdAt;
        $this->availableAt = $createdAt;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function payload(): string
    {
        return $this->payload;
    }

    public function messageKey(): string
    {
        return $this->messageKey;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function availableAt(): ?\DateTimeImmutable
    {
        return $this->availableAt;
    }

    public function attempts(): int
    {
        return $this->attempts;
    }

    public function lastError(): ?string
    {
        return $this->lastError;
    }

    public function markProcessed(int $attempt, \DateTimeImmutable $now): void
    {
        $this->attempts = $attempt;
        $this->processedAt = $now;
        $this->dispatchedAt = $now;
        $this->lastError = null;
    }

    public function markRetry(int $attempt, ?string $error, \DateTimeImmutable $availableAt): void
    {
        $this->attempts = $attempt;
        $this->lastError = $error;
        $this->availableAt = $availableAt;
    }

    public function markDeadLetter(int $attempt, ?string $error, \DateTimeImmutable $now): void
    {
        $this->attempts = $attempt;
        $this->lastError = $error;
        $this->deadLetteredAt = $now;
    }

    public function isReady(\DateTimeImmutable $now): bool
    {
        if (null !== $this->processedAt || null !== $this->deadLetteredAt) {
            return false;
        }

        return null === $this->availableAt || $this->availableAt <= $now;
    }
}
