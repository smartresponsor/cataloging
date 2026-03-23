<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Exception;

final class CategoryNotFound extends \RuntimeException
{
    public function __construct(string $detail = '')
    {
        parent::__construct('Category not found'.('' !== $detail ? ': '.$detail : ''));
    }
}
