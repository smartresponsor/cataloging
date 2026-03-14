<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Tests\Security;

use App\Service\Security\Category\JwkCache;
use App\Service\Security\Category\OidcJwtValidator;
use App\Service\Security\Category\OidcJwtVerifier;
use PHPUnit\Framework\TestCase;

final class CatalogOidcJwtValidatorTest extends TestCase
{
    public function testSignAndValidateRoundTrip(): void
    {
        $cache = new JwkCache();
        $verifier = new OidcJwtVerifier($cache);
        $validator = new OidcJwtValidator($cache);

        $jwt = $verifier->sign(['sub' => 'user-1', 'scope' => 'category:write']);
        $claims = $validator->validate($jwt);

        self::assertSame('user-1', $claims['sub']);
        self::assertSame('category-local', $claims['iss']);
        self::assertSame('category-app', $claims['aud']);
    }
}
