<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\OidcJwtValidatorInterface;
use App\ServiceInterface\OidcJwtVerifierInterface;

final class OidcJwtValidator implements OidcJwtValidatorInterface
{
    public function __construct(
        private readonly ?OidcJwtVerifierInterface $verifier = null,
    ) {
    }

    public function validate(string $jwt): array
    {
        if (null === $this->verifier) {
            throw new \LogicException('OIDC JWT verifier is not configured.');
        }

        return $this->verifier->verify($jwt);
    }
}
