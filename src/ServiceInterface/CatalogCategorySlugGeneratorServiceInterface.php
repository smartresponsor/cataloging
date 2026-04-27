<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\ValueObject\CategorySlugGenerationRequest;

/** Generates and normalizes locale-dependent slugs; applies conflict auto-suffix when needed. */
interface CatalogCategorySlugGeneratorServiceInterface
{
    /**
     * @return array<string,string> normalized slugs after collision policy
     */
    public function generate(CategorySlugGenerationRequest $request): array;
}
