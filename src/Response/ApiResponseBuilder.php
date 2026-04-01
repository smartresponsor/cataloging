<?php

declare(strict_types=1);

namespace App\Response;

final class ApiResponseBuilder
{
    /** @param array<string,mixed> $payload */
    public function success(array $payload = [], int $status = 200): array
    {
        return ['ok' => true, ...$payload, '_status' => $status];
    }

    /** @param list<string> $errors */
    public function error(string $error, array $errors = [], int $status = 400): array
    {
        return [
            'ok' => false,
            'error' => $error,
            'errors' => [] !== $errors ? $errors : [$error],
            '_status' => $status,
        ];
    }
}
