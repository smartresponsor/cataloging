<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service\Security;

/**
 * Provides the jwk cache application service.
 */
final readonly class JwkCache
{
    /**
     * Initializes the jwk cache service collaborators.
     */
    public function __construct(private string $privateKeyPath)
    {
    }

    /**
     * Returns the private key value.
     */
    public function getPrivateKey(): string
    {
        return file_exists($this->privateKeyPath) ? (string) file_get_contents($this->privateKeyPath) : '';
    }
}
