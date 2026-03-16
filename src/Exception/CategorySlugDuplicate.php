<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Exception;

final class testsSlugDuplicate extends \RuntimeException
{
    public function __construct(string $detail = '')
    {
        parent::__construct('Duplicate slug in taxonomy'.('' !== $detail ? ': '.$detail : ''));
    }
}
