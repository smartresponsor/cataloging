<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ServiceInterface\CatalogOidcJwtValidatorServiceInterface;
use App\Cataloging\ServiceInterface\CatalogOidcJwtVerifierServiceInterface;

/**
 * Provides the oidc jwt validator application service.
 */
final readonly class CatalogOidcJwtValidatorService implements CatalogOidcJwtValidatorServiceInterface
{
    /**
     * Initializes the oidc jwt validator service collaborators.
     */
    public function __construct(
        private ?CatalogOidcJwtVerifierServiceInterface $verifier = null,
    ) {
    }

    /**
     * Validates the current input against the component rules.
     */
    public function validate(string $jwt): array
    {
        if (null === $this->verifier) {
            throw new \LogicException('OIDC JWT verifier is not configured.');
        }

        return $this->verifier->verify($jwt);
    }
}
