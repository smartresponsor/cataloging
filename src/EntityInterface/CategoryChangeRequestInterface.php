<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\EntityInterface;

use App\ValueObjectInterface\CategoryChangeRequestStateInterface;

interface CategoryChangeRequestInterface
{
    public function requestId(): string;

    public function categoryId(): string;

    public function submittedBy(): string;

    public function summary(): string;

    /** @return array<string,mixed> */
    public function changes(): array;

    public function state(): CategoryChangeRequestStateInterface;

    public function reviewedBy(): ?string;

    public function decisionReason(): ?string;

    public function submittedAt(): \DateTimeImmutable;

    public function reviewedAt(): ?\DateTimeImmutable;
}
