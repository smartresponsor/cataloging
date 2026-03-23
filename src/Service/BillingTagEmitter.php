<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class BillingTagEmitter
{
    public function emit(string $tenant, string $operation, array $payload = []): void
    {
        $entry = [
            'tenant' => $tenant,
            'operation' => $operation,
            'payload' => $payload,
            'ts' => date(DATE_ATOM),
        ];
        file_put_contents('report/category-billing-tag.json', json_encode($entry, JSON_PRETTY_PRINT));
    }
}
