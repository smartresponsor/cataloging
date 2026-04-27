<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CatalogOutboxMessageEntity;
use App\Cataloging\Observability\CatalogProjectionMetrics;
use App\Cataloging\OutboxInterface\CategoryOutboxRetryInterface;
use App\Cataloging\ProjectionInterface\CategoryProjectionSyncInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CatalogProjectionWorkerService
{
    public function __construct(
        private ?EntityManagerInterface $entityManager,
        private CategoryProjectionSyncInterface $projectionSync,
        private CategoryOutboxRetryInterface $retry,
        private ?CatalogProjectionMetrics $projectionMetrics = null,
        private int $maxAttempts = 5,
    ) {
    }

    public function runOnce(int $limit = 100): int
    {
        if (!$this->entityManager instanceof EntityManagerInterface) {
            return 0;
        }

        $now = new \DateTimeImmutable('now');
        $processed = 0;
        $entities = $this->entityManager->getRepository(CatalogOutboxMessageEntity::class)->findBy([], ['createdAt' => 'ASC']);
        foreach ($entities as $entity) {
            if (!$entity instanceof CatalogOutboxMessageEntity || !$entity->isReady($now)) {
                continue;
            }

            $attempt = $entity->attempts() + 1;
            $event = [
                'id' => $entity->id(),
                'type' => $entity->type(),
                'key' => $entity->messageKey(),
                'payload' => $this->decodePayload($entity->payload()),
                'attempt' => $attempt,
                'createdAt' => $entity->createdAt()->format('Y-m-d H:i:s'),
            ];

            try {
                $this->projectionSync->apply($event);
                $entity->markProcessed($attempt, $now);
                $this->updateLagMetric($event['createdAt'], $now);
            } catch (\Throwable $exception) {
                $message = substr($exception->getMessage(), 0, 2000);
                if ($attempt >= $this->maxAttempts) {
                    $entity->markDeadLetter($attempt, $message, $now);
                } else {
                    $entity->markRetry($attempt, $message, $this->retry->nextRunAt($now, $attempt));
                }
            }

            ++$processed;
            if ($processed >= max(1, $limit)) {
                break;
            }
        }

        $this->entityManager->flush();

        return $processed;
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

        $this->projectionMetrics->setLag(max(0, $now->getTimestamp() - $createdAtTime->getTimestamp()));
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
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
