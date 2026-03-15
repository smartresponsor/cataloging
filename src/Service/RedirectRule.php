<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class RedirectRule
{
    private string $from;
    private string $to;
    private int $status;

    public function __construct(string $from, string $to, int $status = 301)
    {
        $this->from = $from;
        $this->to = $to;
        $this->status = $status;
    }

    public function from(): string
    {
        return $this->from;
    }

    public function to(): string
    {
        return $this->to;
    }

    public function status(): int
    {
        return $this->status;
    }
}
