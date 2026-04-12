<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Observability\CatalogProjectionMetrics;
use App\OutboxInterface\CategoryOutboxRetryInterface;
use App\ProjectionInterface\CategoryProjectionSyncInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

/**
 * Provides the projection worker application service.
 */
final readonly class ProjectionWorker
{
    /**
     * Initializes the projection worker service collaborators.
     */
    public function __construct(
        private Connection $connection,
        private CategoryProjectionSyncInterface $projectionSync,
        private CategoryOutboxRetryInterface $retry,
        private ?CatalogProjectionMetrics $projectionMetrics = null,
        private int $maxAttempts = 5,
    ) {
    }

    /**
     * Handles the run once workflow.
     *
     * @throws \Throwable
     */
    public function runOnce(int $limit = 100): int
    {
        $now = new \DateTimeImmutable('now');
        $rows = $this->connection->fetchAllAssociative(
            'SELECT id, type, payload, "key", created_at, available_at, attempts '
            .'FROM outbox '
            .'WHERE processed_at IS NULL AND dead_lettered_at IS NULL '
            .'AND (available_at IS NULL OR available_at <= :now) '
            .'ORDER BY created_at ASC '
            .'LIMIT :limit',
            [
                'now' => $now->format('Y-m-d H:i:s'),
                'limit' => max(1, $limit),
            ],
            [
                'now' => ParameterType::STRING,
                'limit' => ParameterType::INTEGER,
            ],
        );

        $processed = 0;
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $outboxId = $this->stringValue($row['id'] ?? null);
            if ('' === $outboxId) {
                continue;
            }

            $attempt = $this->intValue($row['attempts'] ?? 0) + 1;
            $event = [
                'id' => $outboxId,
                'type' => $this->stringValue($row['type'] ?? null),
                'key' => $this->stringValue($row['key'] ?? null),
                'payload' => $this->decodePayload($row['payload'] ?? null),
                'attempt' => $attempt,
                'createdAt' => $this->stringValue($row['created_at'] ?? null),
            ];

            try {
                $this->projectionSync->apply($event);
                $this->markProcessed($outboxId, $attempt, $now);
                $this->updateLagMetric($row['created_at'] ?? null, $now);
            } catch (\Throwable $exception) {
                $this->markFailure($outboxId, $event, $attempt, $now, $exception);
            }

            ++$processed;
        }

        return $processed;
    }

    /**
     * @param array<string,mixed> $event
     *
     * @throws \Throwable
     */
    private function markFailure(
        string $outboxId,
        array $event,
        int $attempt,
        \DateTimeImmutable $now,
        \Throwable $exception,
    ): void {
        $this->retry->schedule($event, $attempt);
        $message = substr($exception->getMessage(), 0, 2000);

        if ($attempt >= $this->maxAttempts) {
            $this->connection->update(
                'outbox',
                [
                    'attempts' => $attempt,
                    'last_error' => $message,
                    'dead_lettered_at' => $now->format('Y-m-d H:i:s'),
                ],
                ['id' => $outboxId],
                [
                    'attempts' => ParameterType::INTEGER,
                    'last_error' => ParameterType::STRING,
                    'dead_lettered_at' => ParameterType::STRING,
                    'id' => ParameterType::STRING,
                ],
            );

            return;
        }

        $nextRunAt = $this->retry->nextRunAt($now, $attempt);
        $this->connection->update(
            'outbox',
            [
                'attempts' => $attempt,
                'last_error' => $message,
                'available_at' => $nextRunAt->format('Y-m-d H:i:s'),
            ],
            ['id' => $outboxId],
            [
                'attempts' => ParameterType::INTEGER,
                'last_error' => ParameterType::STRING,
                'available_at' => ParameterType::STRING,
                'id' => ParameterType::STRING,
            ],
        );
    }

    /**
     * @throws \Throwable
     */
    private function markProcessed(string $outboxId, int $attempt, \DateTimeImmutable $now): void
    {
        $this->connection->update(
            'outbox',
            [
                'attempts' => $attempt,
                'processed_at' => $now->format('Y-m-d H:i:s'),
                'dispatched_at' => $now->format('Y-m-d H:i:s'),
                'last_error' => null,
            ],
            ['id' => $outboxId],
            [
                'attempts' => ParameterType::INTEGER,
                'processed_at' => ParameterType::STRING,
                'dispatched_at' => ParameterType::STRING,
                'last_error' => ParameterType::NULL,
                'id' => ParameterType::STRING,
            ],
        );
    }

    private function updateLagMetric(mixed $createdAt, \DateTimeImmutable $now): void
    {
        if (null === $this->projectionMetrics) {
            return;
        }
        $createdAtString = $this->stringValue($createdAt);
        if ('' === $createdAtString) {
            return;
        }

        try {
            $createdAtTime = new \DateTimeImmutable($createdAtString);
        } catch (\Throwable) {
            return;
        }

        $lag = max(0, $now->getTimestamp() - $createdAtTime->getTimestamp());
        $this->projectionMetrics->setLag($lag);
    }

    /** @return array<string,mixed> */
    private function decodePayload(mixed $payload): array
    {
        if (!is_string($payload) || '' === $payload) {
            return [];
        }
        $decoded = json_decode($payload, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function stringValue(mixed $value): string
    {
        if (!is_scalar($value)) {
            return '';
        }

        return trim((string) $value);
    }

    private function intValue(mixed $value): int
    {
        return is_int($value) ? $value : (is_numeric($value) ? (int) $value : 0);
    }
}
