<?php

declare(strict_types=1);

namespace App\LayerInterface\Security;

interface OidcJwtVerifierInterface
{
    public function verify(string $jwt): array;
}
