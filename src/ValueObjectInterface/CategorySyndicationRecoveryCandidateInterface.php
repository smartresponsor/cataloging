<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

interface CategorySyndicationRecoveryCandidateInterface
{
    public function deliveryId(): string;

    public function packageId(): string;

    public function destinationId(): string;

    public function categoryId(): string;

    public function attempt(): int;

    public function responseCode(): ?int;

    public function responseMessage(): string;

    public function retryable(): bool;
}
