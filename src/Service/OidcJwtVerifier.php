<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service;

use App\ServiceInterface\OidcJwtVerifierInterface;

final class OidcJwtVerifier implements OidcJwtVerifierInterface
{
    private string $issuer;
    private string $audience;
    /** @var array<string,string> */
    private array $pemByKid = [];

    public function __construct(string $issuer, string $audience, array $jwkSet)
    {
        $this->issuer = $issuer;
        $this->audience = $audience;
        $conv = new JwkConverter();
        foreach (($jwkSet['keys'] ?? []) as $jwk) {
            if (($jwk['kty'] ?? '') === 'RSA' && isset($jwk['kid'], $jwk['n'], $jwk['e'])) {
                $this->pemByKid[(string) $jwk['kid']] = $conv->rsaToPem((string) $jwk['n'], (string) $jwk['e']);
            }
        }
        if ([] === $this->pemByKid) {
            throw new \InvalidArgumentException('No RSA keys found in JWKS');
        }
    }

    public function verify(string $jwt): array
    {
        [$h64, $p64, $s64] = explode('.', $jwt);
        $header = json_decode($this->b64u($h64), true, 512, JSON_THROW_ON_ERROR);
        $payload = json_decode($this->b64u($p64), true, 512, JSON_THROW_ON_ERROR);
        $sig = $this->b64u($s64);
        $kid = (string) ($header['kid'] ?? '');
        $alg = (string) ($header['alg'] ?? '');
        if ('RS256' !== $alg) {
            throw new \InvalidArgumentException('Unsupported alg');
        }
        $pem = $this->pemByKid[$kid] ?? null;
        if (!$pem) {
            throw new \InvalidArgumentException('Unknown kid');
        }
        $ok = openssl_verify($h64.'.'.$p64, $sig, $pem, OPENSSL_ALGO_SHA256);
        if (1 !== $ok) {
            throw new \RuntimeException('Signature verify failed');
        }
        $now = time();
        if (isset($payload['exp']) && $now >= (int) $payload['exp']) {
            throw new \RuntimeException('Token expired');
        }
        if (isset($payload['nbf']) && $now < (int) $payload['nbf']) {
            throw new \RuntimeException('Token not yet valid');
        }
        if (isset($payload['iss']) && $payload['iss'] !== $this->issuer) {
            throw new \RuntimeException('Invalid issuer');
        }
        if (isset($payload['aud'])) {
            $aud = is_array($payload['aud']) ? $payload['aud'] : [$payload['aud']];
            if (!in_array($this->audience, $aud, true)) {
                throw new \RuntimeException('Invalid audience');
            }
        }

        return $payload;
    }

    private function b64u(string $s): string
    {
        $pad = strlen($s) % 4;
        if (0 !== $pad) {
            $s .= str_repeat('=', 4 - $pad);
        }

        return (string) base64_decode(strtr($s, '-_', '+/'), true);
    }
}
