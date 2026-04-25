<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Idempotency;

use App\Cataloging\Entity\CatalogCategoryIdempotencyEntity;
use App\Cataloging\IdempotencyInterface\CategoryIdempotencyStoreInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Durable DB-backed idempotency store.
 *
 * Uses Doctrine ORM as the durable model.
 */
final readonly class CategoryIdempotencyStore implements CategoryIdempotencyStoreInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function acquire(
        string $key,
        string $operation,
        string $requestHash,
        int $ttlSec,
        ?string $correlationId = null,
    ): bool {
        $normalizedKey = trim($key);
        $normalizedOperation = trim($operation);
        $normalizedRequestHash = trim($requestHash);
        if ('' === $normalizedKey) {
            throw new \InvalidArgumentException('Idempotency key must not be empty.');
        }
        if ('' === $normalizedOperation) {
            throw new \InvalidArgumentException('Idempotency operation must not be empty.');
        }
        if ('' === $normalizedRequestHash) {
            throw new \InvalidArgumentException('Idempotency request hash must not be empty.');
        }

        $now = new \DateTimeImmutable('now');
        $expiresAt = $now->modify(sprintf('+%d seconds', max(1, $ttlSec)));
        $normalizedCorrelationId = null !== $correlationId ? trim($correlationId) : null;
        if ('' === $normalizedCorrelationId) {
            $normalizedCorrelationId = null;
        }

        return $this->acquireWithDoctrine(
            $normalizedKey,
            $normalizedOperation,
            $normalizedRequestHash,
            $now,
            $expiresAt,
            $normalizedCorrelationId,
        );
    }

    public function purgeExpired(): int
    {
        $now = new \DateTimeImmutable('now');

        $expired = $this->entityManager->createQuery('SELECT i FROM App\\Cataloging\\Entity\\CatalogCategoryIdempotencyEntity i WHERE i.expiresAt <= :now')
            ->setParameter('now', $now)
            ->toIterable();

        $deleted = 0;
        foreach ($expired as $entity) {
            if (!$entity instanceof CatalogCategoryIdempotencyEntity) {
                continue;
            }

            $this->entityManager->remove($entity);
            ++$deleted;
        }

        if ($deleted > 0) {
            $this->entityManager->flush();
        }

        return $deleted;
    }

    private function acquireWithDoctrine(
        string $key,
        string $operation,
        string $requestHash,
        \DateTimeImmutable $now,
        \DateTimeImmutable $expiresAt,
        ?string $correlationId,
    ): bool {
        $existing = $this->entityManager->find(CatalogCategoryIdempotencyEntity::class, $key);
        if ($existing instanceof CatalogCategoryIdempotencyEntity) {
            if ($existing->isExpiredAt($now)) {
                $this->entityManager->remove($existing);
                $this->entityManager->flush();
            } elseif ($existing->getOperation() === $operation && $existing->getRequestHash() === $requestHash) {
                return false;
            } else {
                throw new \DomainException(sprintf('Idempotency key "%s" cannot be reused for a different request payload.', $key));
            }
        }

        $entity = new CatalogCategoryIdempotencyEntity($key, $operation, $requestHash, $now, $expiresAt, $correlationId);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return true;
    }
}
