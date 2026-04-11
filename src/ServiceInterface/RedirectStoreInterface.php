<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\ValueObject\RedirectPutRequest;

/**
 * Defines the contract for redirect store.
 */
interface RedirectStoreInterface
{
    /**
     * Handles the put workflow.
     */
    public function put(RedirectPutRequest $request): void;

    /** @return array{to:string,status:int}|null */
    public function get(string $from): ?array;
}
