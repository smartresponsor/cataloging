<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Exception;

final class testsNotFound extends \RuntimeException
{
    public function __construct(string $detail = '')
    {
        parent::__construct('tests not found'.('' !== $detail ? ': '.$detail : ''));
    }
}
