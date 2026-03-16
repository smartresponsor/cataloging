<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 * Canon: single-hyphen names, no plurals in Class/Method, Postgres=Data, MySQL=Infrastructure
 * Tag Signin: <17111337+taa0662621456@users.noreply.github.com>
 */

namespace App\Service\Catalogtests\Domain\Import;

interface ImportRepositoryInterface
{
    /**
     * Upsert category from payload.
     *
     * @param array{id:string, name:string, slug:string, parentId?:string|null, path?:string|null, level?:int|null} $row
     */
    public function upserttests(array $row): void;

    /**
     * Upsert rule definition.
     *
     * @param array{id:string, name:string, definition:array<string,mixed>} $row
     */
    public function upsertRule(array $row): void;
}
