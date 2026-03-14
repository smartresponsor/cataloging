<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Importer;

use App\ImporterInterface\CategoryNdjsonImporterInterface;
use App\ServiceInterface\Command\Category\CategoryCommandServiceInterface as CategoryService;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class CategoryNdjsonImporter implements CategoryNdjsonImporterInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly CategoryService $service,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function import(string $path, bool $dryRun = true): array
    {
        $ok = 0;
        $fail = 0;
        $warnings = 0;
        $report = [];
        $lineNumber = 0;

        $handle = fopen($path, 'r');
        if (false === $handle) {
            throw new \RuntimeException('The NDJSON file could not be opened.');
        }

        while (($line = fgets($handle)) !== false) {
            ++$lineNumber;
            $line = trim($line);

            if ('' === $line) {
                continue;
            }

            $data = json_decode($line, true);
            if (!is_array($data) || empty($data['type'])) {
                ++$fail;
                $report[] = sprintf('Row %d could not be imported because the payload is invalid.', $lineNumber);
                continue;
            }

            try {
                if ('taxonomy' === $data['type']) {
                    ++$warnings;
                    $report[] = sprintf('Row %d was skipped because taxonomy creation is handled separately.', $lineNumber);
                } elseif ('category' === $data['type']) {
                    if (!$dryRun) {
                        $actorId = $data['actorId'] ?? 'system';
                        $this->service->create(
                            $actorId,
                            (string) $data['taxonomyId'],
                            $data['parentId'] ?? null,
                            (array) $data['name'],
                            (array) $data['slug'],
                            (array) ($data['meta'] ?? []),
                        );
                    }
                    ++$ok;
                } elseif ('link' === $data['type']) {
                    if (!$dryRun) {
                        $actorId = $data['actorId'] ?? 'system';
                        $this->service->attach(
                            $actorId,
                            (string) $data['categoryId'],
                            (string) $data['targetDomain'],
                            (string) $data['targetClass'],
                            (string) $data['targetId'],
                        );
                    }
                    ++$ok;
                } else {
                    ++$fail;
                    $report[] = sprintf('Row %d has an unsupported record type "%s".', $lineNumber, (string) $data['type']);
                }
            } catch (\Throwable $throwable) {
                ++$fail;
                $message = $this->humanMessage($throwable, 'The row could not be imported.');
                $report[] = sprintf('Row %d failed: %s', $lineNumber, $message);
                $this->logger->error('Category NDJSON import row failed.', [
                    'path' => $path,
                    'line' => $lineNumber,
                    'type' => $data['type'] ?? null,
                    'dryRun' => $dryRun,
                    'exception' => $throwable,
                ]);
            }
        }

        fclose($handle);

        return [
            'ok' => $ok,
            'fail' => $fail,
            'warnings' => $warnings,
            'report' => $report,
        ];
    }

    private function humanMessage(\Throwable $throwable, string $fallback): string
    {
        $message = trim($throwable->getMessage());

        if ('' === $message) {
            return $fallback;
        }

        return rtrim(ucfirst($message), '.').'.';
    }
}
