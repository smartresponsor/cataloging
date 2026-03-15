<?php

declare(strict_types=1);

namespace App\Service;

interface OidcJwtVerifierInterface
{
    public function verify(string $jwt): array;
}
