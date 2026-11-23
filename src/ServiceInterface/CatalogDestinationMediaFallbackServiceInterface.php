<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CategoryDestinationMediaFallbackEvaluatedInterface;

interface CatalogDestinationMediaFallbackServiceInterface
{
    public function evaluate(string $destinationId, string $categoryId, string $actorId, string $reason): CategoryDestinationMediaFallbackEvaluatedInterface;
}
