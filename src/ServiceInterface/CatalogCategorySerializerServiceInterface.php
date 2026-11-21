<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\ValueObject\CategorySerializationRequest;

/**
 * Defines the contract for category serializer.
 */
interface CatalogCategorySerializerServiceInterface
{
    /** @return array<string,mixed> */
    public function serialize(CategorySerializationRequest $request): array;
}
