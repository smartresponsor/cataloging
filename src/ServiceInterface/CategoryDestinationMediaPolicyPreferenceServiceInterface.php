<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryDestinationMediaPolicyPreferenceEvaluatedInterface;

interface CategoryDestinationMediaPolicyPreferenceServiceInterface
{
    public function evaluate(string $destinationId, string $categoryId, string $actorId, string $reason): CategoryDestinationMediaPolicyPreferenceEvaluatedInterface;
}
