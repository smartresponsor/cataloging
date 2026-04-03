<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\OidcJwtVerifierInterface;

final class OidcJwtVerifier implements OidcJwtVerifierInterface
{
    private string $issuer;
    private string $audience;
    /** @var array<string,string> */
    private array $pemByKid = [];

    /** @param array<string,mixed> $jwkSet */
    public function __construct(string $issuer, string $audience, array $jwkSet)
    {
        $this->issuer = $issuer;
        $this->audience = $audience;
        $converter = new JwkConverter();
        $keys = $jwkSet['keys'] ?? [];
        if (is_array($keys)) {
            foreach ($keys as $jwk) {
                if (!is_array($jwk)) {
                    continue;
                }
                $kid = $this->scalarString($jwk['kid'] ?? null);
                $n = $this->scalarString($jwk['n'] ?? null);
                $e = $this->scalarString($jwk['e'] ?? null);
                if (($jwk['kty'] ?? null) === 'RSA' && '' !== $kid && '' !== $n && '' !== $e) {
                    $this->pemByKid[$kid] = $converter->rsaToPem($n, $e);
                }
            }
        }
        if ([] === $this->pemByKid) {
            throw new \InvalidArgumentException('No RSA keys found in JWKS');
        }
    }

    /** @return array<string,mixed> */
    public function verify(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (3 !== count($parts)) {
            throw new \InvalidArgumentException('Malformed JWT');
        }
        [$h64, $p64, $s64] = $parts;
        $header = $this->decodeJsonObject($this->b64u($h64));
        $payload = $this->decodeJsonObject($this->b64u($p64));
        $sig = $this->b64u($s64);
        $kid = $this->scalarString($header['kid'] ?? null);
        $alg = $this->scalarString($header['alg'] ?? null);
        if ('RS256' !== $alg) {
            throw new \InvalidArgumentException('Unsupported alg');
        }
        $pem = $this->pemByKid[$kid] ?? null;
        if (null === $pem) {
            throw new \InvalidArgumentException('Unknown kid');
        }
        if (1 !== openssl_verify($h64.'.'.$p64, $sig, $pem, OPENSSL_ALGO_SHA256)) {
            throw new \RuntimeException('Signature verify failed');
        }
        $now = time();
        $this->assertTimeClaim($payload, 'exp', static fn (int $claim, int $current): bool => $current < $claim, 'Token expired', $now);
        $this->assertTimeClaim($payload, 'nbf', static fn (int $claim, int $current): bool => $current >= $claim, 'Token not yet valid', $now);
        $iss = $this->scalarString($payload['iss'] ?? null);
        if ('' === $iss) {
            throw new \RuntimeException('Missing issuer');
        }
        if ($iss !== $this->issuer) {
            throw new \RuntimeException('Invalid issuer');
        }
        $aud = $payload['aud'] ?? null;
        $audiences = is_array($aud) ? array_values(array_filter(array_map(fn ($v) => is_scalar($v) ? (string) $v : null, $aud))) : (null !== $aud ? [$this->scalarString($aud)] : []);
        if ([] === $audiences) {
            throw new \RuntimeException('Missing audience');
        }
        if (!in_array($this->audience, $audiences, true)) {
            throw new \RuntimeException('Invalid audience');
        }

        return $payload;
    }

    private function b64u(string $value): string
    {
        $pad = strlen($value) % 4;
        if (0 !== $pad) {
            $value .= str_repeat('=', 4 - $pad);
        }
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);
        if (!is_string($decoded)) {
            throw new \InvalidArgumentException('Invalid base64url payload');
        }

        return $decoded;
    }

    /** @return array<string,mixed> */
    private function decodeJsonObject(string $json): array
    {
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            throw new \InvalidArgumentException('Invalid JSON object');
        }

        return $decoded;
    }

    /** @param array<string,mixed> $payload @param callable(int,int):bool $predicate */
    private function assertTimeClaim(array $payload, string $key, callable $predicate, string $message, int $now): void
    {
        $value = $payload[$key] ?? null;
        if (null === $value || !is_numeric($value)) {
            return;
        }
        if (!$predicate((int) $value, $now)) {
            throw new \RuntimeException($message);
        }
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
