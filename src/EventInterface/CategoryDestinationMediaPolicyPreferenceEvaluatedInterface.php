<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EventInterface;

/**
 * Defines the contract for category destination media policy preference evaluated.
 */
/** @noinspection PhpClassNamingConventionInspection */
interface CategoryDestinationMediaPolicyPreferenceEvaluatedInterface
{
    /** @return array<string,mixed> */
    public function payload(): array;
}
