<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Security;

final class OidcJwtVerifier
{
    public function __construct(
        private readonly JwkCache $jwkCache,
        private readonly string $issuer,
        private readonly string $audience,
    ) {
    }

    public function sign(array $payload): string
    {
        $key = $this->jwkCache->getPrivateKey();
        $now = new \DateTimeImmutable();
        $claims = array_merge($payload, [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'iat' => $now->getTimestamp(),
            'exp' => $now->modify('+5 minutes')->getTimestamp(),
        ]);

        // placeholder signer, real proj can use firebase/jwt
        return base64_encode(json_encode($claims, JSON_THROW_ON_ERROR)).'.sig';
    }
}
