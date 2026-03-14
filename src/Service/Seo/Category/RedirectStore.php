<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
*/

namespace App\Service\Seo\Category;

use App\ServiceInterface\Query\Category\RedirectStoreInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class RedirectStore implements RedirectStoreInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly \PDO $pdo,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function put(string $from, string $to, int $status = 301): void
    {
        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO seo_redirect(from_path, to_path, status) VALUES(:f,:t,:s) '
                .'ON CONFLICT (from_path) DO UPDATE SET to_path = EXCLUDED.to_path, status = EXCLUDED.status'
            );
            $statement->bindValue(':f', $from);
            $statement->bindValue(':t', $to);
            $statement->bindValue(':s', $status);
            $statement->execute();
        } catch (\Throwable $throwable) {
            $this->logger->error('The category redirect could not be stored.', [
                'from' => $from,
                'to' => $to,
                'status' => $status,
                'exception' => $throwable,
            ]);

            throw new \RuntimeException('The redirect could not be stored. Check the logs if the problem continues.', 0, $throwable);
        }
    }

    public function get(string $from): ?array
    {
        try {
            $statement = $this->pdo->prepare(
                'SELECT from_path, to_path, status FROM seo_redirect WHERE from_path = :f LIMIT 1'
            );
            $statement->bindValue(':f', $from);
            $statement->execute();
            $row = $statement->fetch(\PDO::FETCH_ASSOC);

            return $row
                ? ['from' => (string) $row['from_path'], 'to' => (string) $row['to_path'], 'status' => (int) $row['status']]
                : null;
        } catch (\Throwable $throwable) {
            $this->logger->error('The category redirect lookup failed.', [
                'from' => $from,
                'exception' => $throwable,
            ]);

            throw new \RuntimeException('The redirect could not be loaded. Check the logs if the problem continues.', 0, $throwable);
        }
    }
}
