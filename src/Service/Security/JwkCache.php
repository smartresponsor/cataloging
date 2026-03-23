<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Security;

final class JwkCache
{
    public function __construct(private readonly string $privateKeyPath)
    {
    }

    public function getPrivateKey(): string
    {
        return file_exists($this->privateKeyPath) ? (string) file_get_contents($this->privateKeyPath) : '';
    }
}
