<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

interface CategoryBulkInterface
{
    /**
     * Execute batch operations (create/move/attach/detach).
     *
     * @param string                                             $batchKey idempotency key for the whole batch
     * @param list<array{op:string,payload:array<string,mixed>}> $ops
     *
     * @return array{accepted:int, rejected:int, results:list<mixed>}
     */
    public function execute(string $actorId, string $batchKey, array $ops): array;
}
