<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class RollbackOperation
{
    public function rollback(Version $target): void
    {
        // Application layer should restore state from the target version snapshot.
    }
}
