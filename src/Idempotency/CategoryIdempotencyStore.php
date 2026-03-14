<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Idempotency;

use App\IdempotencyInterface\CategoryIdempotencyStoreInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class CategoryIdempotencyStore implements CategoryIdempotencyStoreInterface
{
    private LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function seen(string $key): bool
    {
        $state = $this->loadState();
        $this->purgeExpired($state);

        return array_key_exists($key, $state);
    }

    public function mark(string $key, int $ttlSec): void
    {
        if ('' === $key) {
            throw new \RuntimeException('The idempotency key must not be empty.');
        }

        $state = $this->loadState();
        $this->purgeExpired($state);
        $state[$key] = time() + max(1, $ttlSec);
        $this->saveState($state);
    }

    /**
     * @return array<string,int>
     */
    private function loadState(): array
    {
        $path = $this->statePath();
        if (!is_file($path)) {
            return [];
        }

        $raw = file_get_contents($path);
        if (false === $raw) {
            $this->logger->error('Category idempotency state file could not be read.', ['path' => $path]);

            return [];
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            $this->logger->error('Category idempotency state file is invalid JSON.', [
                'path' => $path,
                'exception' => $exception,
            ]);

            return [];
        }

        if (!is_array($decoded)) {
            return [];
        }

        $state = [];
        foreach ($decoded as $key => $ttl) {
            if (is_string($key) && is_int($ttl)) {
                $state[$key] = $ttl;
            }
        }

        return $state;
    }

    /**
     * @param array<string,int> $state
     */
    private function saveState(array $state): void
    {
        $path = $this->statePath();
        $dir = dirname($path);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('The idempotency state directory could not be created.');
        }

        try {
            $payload = json_encode($state, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
        } catch (\JsonException $exception) {
            throw new \RuntimeException('The idempotency state could not be encoded as JSON.', 0, $exception);
        }

        $written = file_put_contents($path, $payload, LOCK_EX);
        if (false === $written) {
            throw new \RuntimeException('The idempotency state file could not be written.');
        }
    }

    /**
     * @param array<string,int> $state
     */
    private function purgeExpired(array &$state): void
    {
        $now = time();
        $changed = false;

        foreach ($state as $key => $expiresAt) {
            if ($expiresAt < $now) {
                unset($state[$key]);
                $changed = true;
            }
        }

        if ($changed) {
            try {
                $this->saveState($state);
            } catch (\Throwable $throwable) {
                $this->logger->error('Expired idempotency entries could not be persisted.', [
                    'exception' => $throwable,
                ]);
            }
        }
    }

    private function statePath(): string
    {
        return dirname(__DIR__, 4).'/var/category/idempotency/state.json';
    }
}
