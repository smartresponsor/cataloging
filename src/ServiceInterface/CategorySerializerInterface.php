<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\ValueObject\CategorySerializationRequest;

/**
 * Defines the contract for category serializer.
 */
interface CategorySerializerInterface
{
    /** @return array<string,mixed> */
    public function serialize(CategorySerializationRequest $request): array;
}
