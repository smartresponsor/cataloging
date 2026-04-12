<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Symfony\Component\Uid\Uuid;

/**
 * Provides the outbox writer application service.
 */
final readonly class OutboxWriter
{
    /**
     * Initializes the outbox writer service collaborators.
     */
    public function __construct(private Connection $connection)
    {
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @throws Exception
     * @throws \JsonException
     */
    public function append(string $type, array $payload, string $key): void
    {
        $this->connection->executeStatement(
            'INSERT INTO outbox (id, type, payload, "key", created_at) VALUES (:id, :type, :payload, :key, :createdAt) '
            .'ON CONFLICT ("key") DO NOTHING',
            [
                'id' => Uuid::v7()->toRfc4122(),
                'type' => $type,
                'payload' => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
                'key' => $key,
                'createdAt' => (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
            ],
            [
                'id' => ParameterType::STRING,
                'type' => ParameterType::STRING,
                'payload' => ParameterType::STRING,
                'key' => ParameterType::STRING,
                'createdAt' => ParameterType::STRING,
            ],
        );
    }
}
