<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Workflow\Category;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ProjectionWorker
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly \PDO $pdo,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function runOnce(int $limit = 100): int
    {
        $statement = $this->pdo->prepare(
            'SELECT id, type, payload FROM outbox WHERE processed_at IS NULL ORDER BY created_at ASC LIMIT :l'
        );
        $statement->bindValue(':l', $limit, \PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $processed = 0;

        foreach ($rows as $row) {
            try {
                $payload = json_decode((string) $row['payload'], true, 512, JSON_THROW_ON_ERROR);
                if (!is_array($payload)) {
                    throw new \RuntimeException('The outbox payload must decode to an array.');
                }

                $this->apply((string) $row['type'], $payload);

                $update = $this->pdo->prepare(
                    'UPDATE outbox SET processed_at = NOW() WHERE id = :id AND processed_at IS NULL'
                );
                $update->bindValue(':id', (string) $row['id']);
                $update->execute();

                ++$processed;
            } catch (\Throwable $throwable) {
                $this->logger->error('Category projection worker item failed.', [
                    'id' => $row['id'] ?? null,
                    'type' => $row['type'] ?? null,
                    'exception' => $throwable,
                ]);
            }
        }

        return $processed;
    }

    private function apply(string $type, array $payload): void
    {
        if ('' === $type) {
            throw new \RuntimeException('The projection event type must not be empty.');
        }

        if ([] === $payload) {
            throw new \RuntimeException('The projection payload must not be empty.');
        }
    }
}
