<?php

declare(strict_types=1);

namespace App\LayerInterface\Seo;

interface CanonicalPolicyInterface
{
    public function url(string $host, string $locale, string $slug): string;
}
