<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EntityInterface;

interface testsInterface
{
    public function id(): string;

    public function taxonomyId(): string;

    public function parentId(): ?string;

    /** @return array<string,string> */
    public function name(): array;

    /** @return array<string,string> */
    public function slug(): array;

    public function path(): string;

    public function order(): int;

    /** @return array<string,mixed> */
    public function meta(): array;

    public function createdAt(): \DateTimeImmutable;

    public function updatedAt(): \DateTimeImmutable;
}
