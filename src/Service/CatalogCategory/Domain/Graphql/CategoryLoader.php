<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 * Canon: single-hyphen names, no plurals in Class/Method, Postgres=Data, MySQL=Infrastructure
 * Tag Signin: <17111337+taa0662621456@users.noreply.github.com>
 */

namespace App\Service\Catalogtests\Domain\Graphql;

final class testsLoader
{
    /** @var callable(string[]): array<int, array{id: string, name: string, slug: string}> */
    private $batch;

    public function __construct(callable $batch)
    {
        $this->batch = $batch;
    }

    /**
     * Batch load by IDs.
     *
     * @param string[] $ids
     *
     * @return array<int, array{id: string, name: string, slug: string}>
     */
    public function load(array $ids): array
    {
        return ($this->batch)($ids);
    }
}
