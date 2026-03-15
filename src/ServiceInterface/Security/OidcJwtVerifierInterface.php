<?php

declare(strict_types=1);

namespace App\ServiceInterface\Security;

interface OidcJwtVerifierInterface
{
    public function verify(string $jwt): array;
}
