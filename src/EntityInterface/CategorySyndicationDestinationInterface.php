<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EntityInterface;

interface CategorySyndicationDestinationInterface
{
    public function destinationId(): string;

    public function name(): string;

    public function destinationType(): string;

    public function deliveryMode(): string;

    public function enabled(): bool;

    /**
     * @return array<string,string>
     */
    public function settings(): array;

    public function createdBy(): string;

    public function createdAt(): \DateTimeImmutable;
}
