<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Idempotency;

use App\IdempotencyInterface\CategoryIdempotencyStoreInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\ParameterType;

/**
 * Durable DB-backed idempotency store.
 *
 * Uses a unique key in the primary data store so duplicate mutation requests
 * are suppressed across process restarts and multiple nodes.
 */
final class CategoryIdempotencyStore implements CategoryIdempotencyStoreInterface
{
    /**
     * Initializes the category idempotency store service collaborators.
     */
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * Handles the acquire workflow.
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
        $nowValue = $now->format('Y-m-d H:i:s');
        $expiresAtValue = $expiresAt->format('Y-m-d H:i:s');
        $normalizedCorrelationId = null !== $correlationId ? trim($correlationId) : null;
        if ('' === $normalizedCorrelationId) {
            $normalizedCorrelationId = null;
        }

        $this->connection->executeStatement(
            'DELETE FROM category_idempotency WHERE idempotency_key = :key AND expires_at <= :now',
            ['key' => $normalizedKey, 'now' => $nowValue],
            ['key' => ParameterType::STRING, 'now' => ParameterType::STRING],
        );

        try {
            $this->connection->insert('category_idempotency', [
                'idempotency_key' => $normalizedKey,
                'operation' => $normalizedOperation,
                'request_hash' => $normalizedRequestHash,
                'created_at' => $nowValue,
                'expires_at' => $expiresAtValue,
                'correlation_id' => $normalizedCorrelationId,
            ], [
                'idempotency_key' => ParameterType::STRING,
                'operation' => ParameterType::STRING,
                'request_hash' => ParameterType::STRING,
                'created_at' => ParameterType::STRING,
                'expires_at' => ParameterType::STRING,
                'correlation_id' => null === $normalizedCorrelationId ? ParameterType::NULL : ParameterType::STRING,
            ]);

            return true;
        } catch (UniqueConstraintViolationException) {
            $existing = $this->connection->fetchAssociative(
                'SELECT operation, request_hash FROM category_idempotency WHERE idempotency_key = :key LIMIT 1',
                ['key' => $normalizedKey],
                ['key' => ParameterType::STRING],
            );

            if (
                is_array($existing)
                && ($existing['operation'] ?? null) === $normalizedOperation
                && ($existing['request_hash'] ?? null) === $normalizedRequestHash
            ) {
                return false;
            }

            throw new \DomainException(sprintf('Idempotency key "%s" cannot be reused for a different request payload.', $normalizedKey));
        }
    }

    /**
     * Handles the purge expired workflow.
     */
    public function purgeExpired(): int
    {
        $now = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        return (int) $this->connection->executeStatement(
            'DELETE FROM category_idempotency WHERE expires_at <= :now',
            ['now' => $now],
            ['now' => ParameterType::STRING],
        );
    }
}
