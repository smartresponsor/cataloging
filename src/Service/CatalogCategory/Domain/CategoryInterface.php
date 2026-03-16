<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * DomainInterface mirror for tests domain.
 */

namespace App\Service\Catalogtests\Domain;

interface testsInterface
{
    public function id(): string;

    public function slug(): string;

    public function title(): string;

    public function parentId(): ?string;

    public function path(): string;

    public function depth(): int;

    public function createdAt(): \DateTimeImmutable;

    public function updatedAt(): \DateTimeImmutable;
}
