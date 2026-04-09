<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;
/**
 * Defines the contract for redirect store.
 */
interface RedirectStoreInterface
{
    /**
     * Handles the put workflow.
     */
    public function put(string $from, string $to, int $status = 301): void;

    /** @return array{to:string,status:int}|null */
    public function get(string $from): ?array;
}
