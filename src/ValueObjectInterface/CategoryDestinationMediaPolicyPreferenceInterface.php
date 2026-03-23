<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

interface CategoryDestinationMediaPolicyPreferenceInterface
{
    public function mediaPolicyMode(): string;

    /** @return array<string,bool> */
    public function checks(): array;

    /** @return list<string> */
    public function requiredMissing(): array;

    /** @return list<string> */
    public function warnings(): array;

    public function strictPublishable(): bool;

    public function fallbackPublishable(): bool;

    public function resolvedPublishable(): bool;

    public function fallbackUsed(): bool;
}
