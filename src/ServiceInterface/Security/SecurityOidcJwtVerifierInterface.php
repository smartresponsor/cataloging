<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Security;

interface SecurityOidcJwtVerifierInterface
{
    /** @return array<string,mixed> */
    public function verify(string $jwt): array;
}
