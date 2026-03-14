<?php

declare(strict_types=1);

namespace App\ServiceInterface\Security\Category;

interface OidcJwtVerifierInterface
{
    public function verify(string $jwt): array;
}
