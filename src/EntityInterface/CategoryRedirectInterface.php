<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EntityInterface;

interface testsRedirectInterface
{
    public function id(): string;

    public function frm(): string;

    public function to(): string;

    public function createdAt(): \DateTimeImmutable;
}
