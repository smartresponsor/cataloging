<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Service\Integration\Category;

use App\ServiceInterface\Integration\Category\WebhookAdminServiceInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class WebhookAdminService implements WebhookAdminServiceInterface
{
    private LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function registerKey(string $name): array
    {
        if ('' === trim($name)) {
            throw new \RuntimeException('The webhook key name must not be empty.');
        }

        try {
            $state = $this->loadState();
            $token = bin2hex(random_bytes(16));

            $state['key'][] = [
                'name' => $name,
                'token' => $token,
                'createdAt' => gmdate(DATE_ATOM),
            ];

            $this->saveState($state);

            return ['name' => $name, 'token' => $token];
        } catch (\Throwable $throwable) {
            $this->logger->error('Webhook key registration failed.', [
                'name' => $name,
                'exception' => $throwable,
            ]);

            throw new \RuntimeException('The webhook key could not be registered. Check the logs if the problem continues.', 0, $throwable);
        }
    }

    public function scheduleDelivery(string $target, array $payload): int
    {
        if ('' === trim($target)) {
            throw new \RuntimeException('The webhook target must not be empty.');
        }

        try {
            $state = $this->loadState();
            $nextId = (int) ($state['meta']['nextDeliveryId'] ?? 1);

            $state['delivery'][] = [
                'id' => $nextId,
                'target' => $target,
                'payload' => $payload,
                'status' => 'queued',
                'createdAt' => gmdate(DATE_ATOM),
            ];
            $state['meta']['nextDeliveryId'] = $nextId + 1;

            $this->saveState($state);

            return $nextId;
        } catch (\Throwable $throwable) {
            $this->logger->error('Webhook delivery scheduling failed.', [
                'target' => $target,
                'payload' => $payload,
                'exception' => $throwable,
            ]);

            throw new \RuntimeException('The webhook delivery could not be scheduled. Check the logs if the problem continues.', 0, $throwable);
        }
    }

    public function requeue(int $limit): int
    {
        if ($limit < 1) {
            throw new \RuntimeException('The requeue limit must be greater than zero.');
        }

        try {
            $state = $this->loadState();
            $changed = 0;

            foreach ($state['delivery'] as &$delivery) {
                if ($changed >= $limit) {
                    break;
                }

                if (($delivery['status'] ?? null) === 'dlq') {
                    $delivery['status'] = 'queued';
                    $delivery['requeuedAt'] = gmdate(DATE_ATOM);
                    ++$changed;
                }
            }
            unset($delivery);

            $this->saveState($state);

            return $changed;
        } catch (\Throwable $throwable) {
            $this->logger->error('Webhook DLQ requeue failed.', [
                'limit' => $limit,
                'exception' => $throwable,
            ]);

            throw new \RuntimeException('The webhook delivery queue could not be requeued. Check the logs if the problem continues.', 0, $throwable);
        }
    }

    /**
     * @return array{key:list<array<string,mixed>>,delivery:list<array<string,mixed>>,meta:array<string,mixed>}
     */
    private function loadState(): array
    {
        $path = $this->statePath();
        if (!is_file($path)) {
            return ['key' => [], 'delivery' => [], 'meta' => ['nextDeliveryId' => 1]];
        }

        $raw = file_get_contents($path);
        if (false === $raw) {
            throw new \RuntimeException('The webhook state file could not be read.');
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new \RuntimeException('The webhook state file is not valid JSON.', 0, $exception);
        }

        if (!is_array($decoded)) {
            throw new \RuntimeException('The webhook state file does not contain a valid state payload.');
        }

        return [
            'key' => array_values(is_array($decoded['key'] ?? null) ? $decoded['key'] : []),
            'delivery' => array_values(is_array($decoded['delivery'] ?? null) ? $decoded['delivery'] : []),
            'meta' => is_array($decoded['meta'] ?? null) ? $decoded['meta'] : ['nextDeliveryId' => 1],
        ];
    }

    /**
     * @param array{key:list<array<string,mixed>>,delivery:list<array<string,mixed>>,meta:array<string,mixed>} $state
     */
    private function saveState(array $state): void
    {
        $path = $this->statePath();
        $dir = dirname($path);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('The webhook state directory could not be created.');
        }

        $json = json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $written = file_put_contents($path, $json.PHP_EOL, LOCK_EX);

        if (false === $written) {
            throw new \RuntimeException('The webhook state file could not be written.');
        }
    }

    private function statePath(): string
    {
        return dirname(__DIR__, 4).'/var/category/webhook-admin/state.json';
    }
}
