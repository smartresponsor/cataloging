<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace App\Service;

final class CategorySerializer implements CategorySerializerInterface
{
    public function serialize(array $source, array $includeFieldList, array $excludeFieldList): array
    {
        $result = $source;

        if (!empty($includeFieldList)) {
            $result = array_intersect_key($result, array_flip($includeFieldList));
        }
        if (!empty($excludeFieldList)) {
            foreach ($excludeFieldList as $key) {
                unset($result[$key]);
            }
        }

        return $result;
    }
}
