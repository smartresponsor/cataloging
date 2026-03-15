<?php

declare(strict_types=1);

namespace App\ServiceInterface;

interface ImporterInterface
{
    public function importCsv(string $path): int;

    public function importJson(string $path): int;
}
