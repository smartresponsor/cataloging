<?php

declare(strict_types=1);

namespace App\ServiceInterface\Query\Category;

interface RedirectStoreInterface
{
    public function put(string $from, string $to, int $status = 301): void;

    public function get(string $from): ?array;
}
