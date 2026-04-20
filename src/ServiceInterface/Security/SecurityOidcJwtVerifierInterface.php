<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface\Security;

/**
 * Defines the contract for security oidc jwt verifier.
 */
interface SecurityOidcJwtVerifierInterface
{
    /** @return array<string,mixed> */
    public function verify(string $jwt): array;
}
