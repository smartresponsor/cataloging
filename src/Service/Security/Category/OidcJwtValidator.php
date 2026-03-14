<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Service\Security\Category;

use App\ServiceInterface\Security\Category\OidcJwtValidatorInterface;

final class OidcJwtValidator implements OidcJwtValidatorInterface
{
    public function __construct(
        private readonly JwkCache $jwkCache,
        private readonly string $issuer = 'category-local',
        private readonly string $audience = 'category-app',
    ) {
    }

    public function validate(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (3 !== count($parts)) {
            throw new \InvalidArgumentException('Malformed JWT');
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;
        $header = json_decode(self::base64UrlDecode($headerEncoded), true, 512, JSON_THROW_ON_ERROR);
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true, 512, JSON_THROW_ON_ERROR);

        if (($header['alg'] ?? null) !== 'HS256') {
            throw new \RuntimeException('Unsupported JWT algorithm');
        }

        $expected = self::base64UrlEncode(hash_hmac('sha256', $headerEncoded.'.'.$payloadEncoded, $this->jwkCache->getPrivateKey(), true));
        if (!hash_equals($expected, $signatureEncoded)) {
            throw new \RuntimeException('Invalid JWT signature');
        }

        $now = time();
        if (($payload['iss'] ?? null) !== $this->issuer) {
            throw new \RuntimeException('Invalid issuer');
        }
        if (($payload['aud'] ?? null) !== $this->audience) {
            throw new \RuntimeException('Invalid audience');
        }
        if (isset($payload['nbf']) && (int) $payload['nbf'] > $now) {
            throw new \RuntimeException('Token not yet valid');
        }
        if (!isset($payload['exp']) || (int) $payload['exp'] < $now) {
            throw new \RuntimeException('Token expired');
        }

        return $payload;
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $value): string
    {
        $padding = 4 - (strlen($value) % 4);
        if ($padding < 4) {
            $value .= str_repeat('=', $padding);
        }

        $decoded = base64_decode(strtr($value, '-_', '+/'), true);
        if (false === $decoded) {
            throw new \InvalidArgumentException('Invalid base64url payload');
        }

        return $decoded;
    }
}
