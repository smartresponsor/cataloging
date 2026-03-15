<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\OidcJwtValidatorInterface;

final class OidcJwtValidator implements OidcJwtValidatorInterface
{
    public function validate(string $jwt): array
    {
        // Wire to JWKS cache and perform signature and claim validation.
        // Return claim set if valid, otherwise throw an exception in real implementation.
        return ['sub' => 'example'];
    }
}
