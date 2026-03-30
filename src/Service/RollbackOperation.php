<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class RollbackOperation
{
    public function rollback(Version $target): void
    {
        // Application layer should restore state from the target version snapshot.
    }
}
