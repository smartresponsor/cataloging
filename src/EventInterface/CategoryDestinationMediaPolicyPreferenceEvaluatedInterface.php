<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EventInterface;
/**
 * Defines the contract for category destination media policy preference evaluated.
 */
interface CategoryDestinationMediaPolicyPreferenceEvaluatedInterface
{
    /** @return array<string,mixed> */
    public function payload(): array;
}
