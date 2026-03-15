<?php

declare(strict_types=1);

namespace App\Layer\Security;

interface OidcJwtVerifierInterface
{
    public function verify(string $jwt): array;
}
