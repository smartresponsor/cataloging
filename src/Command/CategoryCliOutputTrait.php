<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

trait CategoryCliOutputTrait
{
    /** @param array<string,mixed>|list<array<string,mixed>> $payload */
    private function writeJson(OutputInterface $output, array $payload): int
    {
        $output->writeln((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return Command::SUCCESS;
    }

    /** @param list<array<string,mixed>> $rows */
    private function writeStructuredRows(OutputInterface $output, array $rows, string $format): int
    {
        if ('json' === $format) {
            return $this->writeJson($output, $rows);
        }

        if ('ndjson' !== $format) {
            $output->writeln((string) json_encode([
                'error' => 'invalid_format',
                'allowed' => ['json', 'ndjson'],
                'received' => $format,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return Command::INVALID;
        }

        foreach ($rows as $row) {
            $output->writeln((string) json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return Command::SUCCESS;
    }
}
