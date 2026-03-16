<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace App\ServiceInterface;

interface CatalogtestsSerializerInterface
{
    public function serialize(array $source, array $includeFieldList, array $excludeFieldList): array;
}
