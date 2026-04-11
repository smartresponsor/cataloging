<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\CategorySerializerInterface;
use App\ValueObject\CategorySerializationRequest;

/**
 * Provides the category serializer application service.
 */
final class CategorySerializer implements CategorySerializerInterface
{
    /** @return array<string,mixed> */
    public function serialize(CategorySerializationRequest $request): array
    {
        $result = $request->source();

        if ([] !== $request->includeFieldList()) {
            $result = array_intersect_key($result, array_flip($request->includeFieldList()));
        }
        foreach ($request->excludeFieldList() as $key) {
            unset($result[$key]);
        }

        return $result;
    }
}
