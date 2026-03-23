<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

interface CategoryWorkflowStateInterface
{
    public function value(): string;

    public function is(string $state): bool;
}
