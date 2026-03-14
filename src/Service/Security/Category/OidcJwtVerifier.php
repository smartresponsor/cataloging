<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Service\Security\Category;

final class OidcJwtVerifier
{
    public function __construct(
        private readonly JwkCache $jwkCache,
        private readonly string $issuer = 'category-local',
        private readonly string $audience = 'category-app',
    ) {
    }

    public function sign(array $payload): string
    {
        $now = new \DateTimeImmutable();
        $claims = array_merge($payload, [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'iat' => $now->getTimestamp(),
            'exp' => $now->modify('+5 minutes')->getTimestamp(),
        ]);

        $header = ['alg' => 'HS256', 'typ' => 'JWT'];

        $headerEncoded = self::base64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR));
        $payloadEncoded = self::base64UrlEncode(json_encode($claims, JSON_THROW_ON_ERROR));
        $signature = hash_hmac('sha256', $headerEncoded.'.'.$payloadEncoded, $this->jwkCache->getPrivateKey(), true);

        return $headerEncoded.'.'.$payloadEncoded.'.'.self::base64UrlEncode($signature);
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
