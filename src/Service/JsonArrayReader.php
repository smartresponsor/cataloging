<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Reads JSON files into normalized array payloads.
 */
final readonly class JsonArrayReader
{
    /** @return array<int|string, mixed> */
    public function read(string $path): array
    {
        if (!is_file($path)) {
            return [];
        }

        $json = file_get_contents($path);
        if (!is_string($json) || '' === $json) {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }
}
