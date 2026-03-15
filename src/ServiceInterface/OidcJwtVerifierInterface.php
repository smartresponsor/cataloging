<?php

declare(strict_types=1);

namespace App\ServiceInterface;

interface OidcJwtVerifierInterface
{
    public function verify(string $jwt): array;
}
