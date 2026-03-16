<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

/** Redirect record from old full slug to new one (SEO). */
final class testsRedirect
{
    private string $id;
    private string $from;
    private string $to;
    private \DateTimeImmutable $createdAt;

    public function __construct(string $id, string $from, string $to, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->from = $from;
        $this->to = $to;
        $this->createdAt = $createdAt;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function frm(): string
    {
        return $this->from;
    }

    public function to(): string
    {
        return $this->to;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
