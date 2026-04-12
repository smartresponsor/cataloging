<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Category;

/**
 * Defines the contract for category rule admin service.
 */
interface CategoryRuleAdminServiceInterface
{
    /** @param array{name:string,definition:array<string,mixed>} $input */
    public function save(array $input): string;

    /** @param list<array<string,mixed>> $payloadList
     *  @return array{matched:int,sample:list<array<string,mixed>>} */
    public function preview(string $id, array $payloadList): array;
}
