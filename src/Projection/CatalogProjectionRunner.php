<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Projection;

use App\Observability\CatalogProjectionMetrics;
use App\ServiceInterface\Workflow\Category\ProjectionRunnerInterface;

final class CatalogProjectionRunner implements ProjectionRunnerInterface
{
    public function __construct(private readonly CatalogProjectionMetrics $metrics)
    {
    }

    public function runOnce(): void
    {
        $root = dirname(__DIR__, 2);
        $sourcePath = $root.'/report/catalog-export-flat.json';
        $rows = [];

        if (is_file($sourcePath)) {
            $decoded = json_decode((string) file_get_contents($sourcePath), true);
            if (is_array($decoded)) {
                $rows = $decoded;
            }
        }

        $count = is_countable($rows) ? count($rows) : 0;
        $this->metrics->setLag(0);

        $outPath = $root.'/report/catalog-projection-run.json';
        file_put_contents($outPath, json_encode([
            'processed' => $count,
            'lag' => $this->metrics->getLag(),
            'generatedAt' => gmdate(DATE_ATOM),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL, LOCK_EX);
    }

    public function lag(): int
    {
        return $this->metrics->getLag();
    }
}
