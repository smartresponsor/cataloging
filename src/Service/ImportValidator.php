<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the import validator application service.
 */
final class ImportValidator
{
    /**
     * @param list<array<string,mixed>> $rows
     *
     * @return list<array{row:int,errors:list<string>}>
     */
    public function validate(array $rows): array
    {
        $errors = [];
        foreach ($rows as $index => $row) {
            $rowErrors = [];
            if ('' === $this->stringValue($row, 'id')) {
                $rowErrors[] = 'missing id';
            }
            if ('' === $this->stringValue($row, 'name')) {
                $rowErrors[] = 'missing name';
            }
            if ([] !== $rowErrors) {
                $errors[] = ['row' => $index, 'errors' => $rowErrors];
            }
        }

        return $errors;
    }

    /** @param array<string,mixed> $row */
    private function stringValue(array $row, string $key): string
    {
        $value = $row[$key] ?? '';

        return is_scalar($value) ? trim((string) $value) : '';
    }
}
