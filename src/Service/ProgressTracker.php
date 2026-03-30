<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class ProgressTracker
{
    private int $ok = 0;
    private int $fail = 0;

    public function report(int $ok, int $fail): void
    {
        $this->ok += $ok;
        $this->fail += $fail;
    }

    public function totalOk(): int
    {
        return $this->ok;
    }

    public function totalFail(): int
    {
        return $this->fail;
    }
}
