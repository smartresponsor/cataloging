<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the jwk converter application service.
 */
final class JwkConverter
{
    /**
     * Handles the rsa to pem workflow.
     */
    public function rsaToPem(string $nB64u, string $eB64u): string
    {
        $modulus = $this->b64u($nB64u);
        $exponent = $this->b64u($eB64u);

        $rsaPublicKey = $this->encodeSequence([
            $this->encodeInteger($modulus),
            $this->encodeInteger($exponent),
        ]);

        $algorithmIdentifier = $this->encodeSequence([
            $this->encodeObjectIdentifier('1.2.840.113549.1.1.1'),
            $this->encodeNull(),
        ]);

        $subjectPublicKeyInfo = $this->encodeSequence([
            $algorithmIdentifier,
            $this->encodeBitString($rsaPublicKey),
        ]);

        return sprintf(
            "-----BEGIN PUBLIC KEY-----\n%s-----END PUBLIC KEY-----\n",
            chunk_split(base64_encode($subjectPublicKeyInfo), 64, "\n")
        );
    }

    private function b64u(string $value): string
    {
        $pad = strlen($value) % 4;
        if (0 !== $pad) {
            $value .= str_repeat('=', 4 - $pad);
        }
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);
        if (!is_string($decoded)) {
            throw new \InvalidArgumentException('Invalid base64url value.');
        }

        return $decoded;
    }

    private function encodeInteger(string $value): string
    {
        if ('' === $value || (ord($value[0]) & 0x80) !== 0) {
            $value = "\x00".$value;
        }

        return "\x02".$this->len(strlen($value)).$value;
    }

    private function encodeNull(): string
    {
        return "\x05\x00";
    }

    private function encodeBitString(string $value): string
    {
        $body = "\x00".$value;

        return "\x03".$this->len(strlen($body)).$body;
    }

    private function encodeObjectIdentifier(string $oid): string
    {
        $parts = array_map('intval', explode('.', $oid));
        if (count($parts) < 2) {
            throw new \InvalidArgumentException('Invalid object identifier.');
        }

        $first = (40 * $parts[0]) + $parts[1];
        $encoded = chr($first);
        foreach (array_slice($parts, 2) as $part) {
            $encoded .= $this->encodeBase128Int($part);
        }

        return "\x06".$this->len(strlen($encoded)).$encoded;
    }

    private function encodeBase128Int(int $value): string
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('OID part must be non-negative.');
        }
        if (0 === $value) {
            return "\x00";
        }

        $bytes = [];
        while ($value > 0) {
            array_unshift($bytes, $value & 0x7F);
            $value >>= 7;
        }
        $lastIndex = count($bytes) - 1;
        foreach ($bytes as $index => $byte) {
            if ($index !== $lastIndex) {
                $bytes[$index] = $byte | 0x80;
            }
        }

        return implode('', array_map(static fn (int $byte): string => chr($byte), $bytes));
    }

    /** @param list<string> $chunks */
    private function encodeSequence(array $chunks): string
    {
        $body = implode('', $chunks);

        return "\x30".$this->len(strlen($body)).$body;
    }

    private function len(int $length): string
    {
        if ($length < 0x80) {
            return chr($length);
        }
        $tmp = '';
        while ($length > 0) {
            $tmp = chr($length & 0xFF).$tmp;
            $length >>= 8;
        }

        return chr(0x80 | strlen($tmp)).$tmp;
    }
}
