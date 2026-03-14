<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Service\Security\Category;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class JwkCache
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly ?string $privateKeyPath = null,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function getPrivateKey(): string
    {
        $path = $this->privateKeyPath;
        if (is_string($path) && '' !== $path && is_file($path)) {
            $raw = file_get_contents($path);
            if (false !== $raw) {
                $key = trim($raw);
                if ('' !== $key) {
                    return $key;
                }
            }

            $this->logger->warning('The JWK private key file could not be read. Falling back to environment configuration.', [
                'path' => $path,
            ]);
        }

        $envSecret = getenv('CATEGORY_JWK_SECRET');
        if (is_string($envSecret) && '' !== $envSecret) {
            return $envSecret;
        }

        $this->logger->warning('Using the local fallback JWK secret. Configure CATEGORY_JWK_SECRET for non-local environments.');

        return 'catalog-local-secret';
    }
}
