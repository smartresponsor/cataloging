<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategoryPublicationQualityProfileInterface;

interface CategoryPublicationQualityPolicyInterface
{
    /**
     * @param array<string,bool> $publicationChecks
     * @param array<string,bool> $checks
     */
    public function buildProfile(int $score, array $publicationChecks, array $checks): CategoryPublicationQualityProfileInterface;
}
