<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\OidcJwtVerifierInterface;

/**
 * Provides the oidc jwt verifier application service.
 */
/** @noinspection PhpCSFixerValidationInspection */
final class OidcJwtVerifier implements OidcJwtVerifierInterface
{
    /** @var array<string,string> */
    private array $pemByKid = [];

    /**
     * @param array<string,mixed> $jwkSet
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(private readonly string $issuer, private readonly string $audience, array $jwkSet)
    {
        $converter = new JwkConverter();
        $keys = $jwkSet['keys'] ?? [];
        if (is_array($keys)) {
            foreach ($keys as $jwk) {
                if (!is_array($jwk)) {
                    continue;
                }
                $kid = $this->scalarString($jwk['kid'] ?? null);
                $modulus = $this->scalarString($jwk['n'] ?? null);
                $exponent = $this->scalarString($jwk['e'] ?? null);
                if (($jwk['kty'] ?? null) === 'RSA' && '' !== $kid && '' !== $modulus && '' !== $exponent) {
                    $this->pemByKid[$kid] = $converter->rsaToPem($modulus, $exponent);
                }
            }
        }
        if ([] === $this->pemByKid) {
            throw new \InvalidArgumentException('No RSA keys found in JWKS');
        }
    }

    /**
     * @param string $jwt
     *
     * @return array<string,mixed>
     *
     * @throws \JsonException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
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
        $this->assertTimeClaim(
            $payload,
            'exp',
            static fn (int $claim, int $current): bool => $current < $claim,
            'Token expired',
            $now,
        );
        $this->assertTimeClaim(
            $payload,
            'nbf',
            static fn (int $claim, int $current): bool => $current >= $claim,
            'Token not yet valid',
            $now,
        );
        $iss = $this->scalarString($payload['iss'] ?? null);
        if ('' === $iss) {
            throw new \RuntimeException('Missing issuer');
        }
        if ($iss !== $this->issuer) {
            throw new \RuntimeException('Invalid issuer');
        }
        $aud = $payload['aud'] ?? null;
        $audiences = is_array($aud)
            ? array_values(array_filter(array_map(fn ($v) => is_scalar($v) ? (string) $v : null, $aud)))
            : (null !== $aud ? [$this->scalarString($aud)] : []);
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

    /**
     * @param string $json
     *
     * @return array<string,mixed>
     *
     * @throws \JsonException
     */
    private function decodeJsonObject(string $json): array
    {
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            throw new \InvalidArgumentException('Invalid JSON object');
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    /**
     * @param array<string,mixed>    $payload
     * @param callable(int,int):bool $predicate
     *
     * @throws \RuntimeException
     */
    private function assertTimeClaim(array $payload, string $key, callable $predicate, string $message, int $now): void
    {
        $value = $payload[$key] ?? null;
        if (null === $value) {
            return;
        }
        if (!is_numeric($value)) {
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
