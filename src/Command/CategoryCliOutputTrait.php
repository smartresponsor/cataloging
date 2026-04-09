<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Provides shared helpers for category cli output trait.
 */
trait CategoryCliOutputTrait
{
    /** @param array<string,mixed>|list<array<string,mixed>> $payload */
    private function writeJson(OutputInterface $output, array $payload): int
    {
        $output->writeln(
            $this->encodeJson(
                $payload,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            ),
        );

        return Command::SUCCESS;
    }

    /** @param list<array<string,mixed>> $rows */
    private function writeStructuredRows(OutputInterface $output, array $rows, string $format): int
    {
        if ('json' === $format) {
            return $this->writeJson($output, $rows);
        }

        if ('ndjson' !== $format) {
            $output->writeln($this->encodeJson([
                'error' => 'invalid_format',
                'allowed' => ['json', 'ndjson'],
                'received' => $format,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return Command::INVALID;
        }

        foreach ($rows as $row) {
            $output->writeln($this->encodeJson($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return Command::SUCCESS;
    }

    /** @param array<string,mixed>|list<array<string,mixed>> $payload */
    private function encodeJson(array $payload, int $flags): string
    {
        return json_encode($payload, $flags | JSON_THROW_ON_ERROR);
    }
}
