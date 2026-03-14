<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Security\Category;

final class JwkConverter
{
    public function rsaToPem(string $nB64u, string $eB64u): string
    {
        $n = $this->b64u($nB64u);
        $e = $this->b64u($eB64u);
        $seq = $this->encodeSequence([$this->encodeInteger($n), $this->encodeInteger($e)]);
        $bitString = "\x03".$this->len(strlen($seq) + 1)."\x00".$seq;
        $algId = "\x30\x0D\x06\x09\x2A\x86\x48\x86\xF7\x0D\x01\x01\x01\x05\x00"; // rsaEncryption
        $spki = "\x30".$this->len(strlen($algId) + strlen($bitString)).$algId.$bitString;
        $pem = "-----BEGIN PUBLIC KEY-----\n".chunk_split(base64_encode($spki), 64, "\n")."-----END PUBLIC KEY-----\n";

        return $pem;
    }

    private function b64u(string $s): string
    {
        $pad = strlen($s) % 4;
        if (0 !== $pad) {
            $s .= str_repeat('=', 4 - $pad);
        }

        return base64_decode(strtr($s, '-_', '+/'), true);
    }

    private function encodeInteger(string $x): string
    {
        if ('' === $x || (ord($x[0]) & 0x80)) {
            $x = "\x00".$x;
        }

        return "\x02".$this->len(strlen($x)).$x;
    }

    private function encodeSequence(array $chunks): string
    {
        $body = implode('', $chunks);

        return "\x30".$this->len(strlen($body)).$body;
    }

    private function len(int $l): string
    {
        if ($l < 0x80) {
            return chr($l);
        }
        $tmp = '';
        while ($l > 0) {
            $tmp = chr($l & 0xFF).$tmp;
            $l >>= 8;
        }

        return chr(0x80 | strlen($tmp)).$tmp;
    }
}
