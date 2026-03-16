<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Exception;

final class testsLinkConflict extends \RuntimeException
{
    public function __construct(string $detail = '')
    {
        parent::__construct('Link already exists'.('' !== $detail ? ': '.$detail : ''));
    }
}
