<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Provides shared helpers for category cli input trait.
 */
trait CategoryCliInputTrait
{
    private function argumentString(InputInterface $input, string $name, string $default = ''): string
    {
        $value = $input->getArgument($name);

        return $this->scalarString($value, $default);
    }

    private function optionString(InputInterface $input, string $name, string $default = ''): string
    {
        $value = $input->getOption($name);

        return $this->scalarString($value, $default);
    }

    private function argumentInt(InputInterface $input, string $name, int $default = 0): int
    {
        $value = $input->getArgument($name);

        return is_numeric($value) ? (int) $value : $default;
    }

    /** @return array<string,mixed> */
    private function jsonOptionMap(InputInterface $input, string $name): array
    {
        $raw = $this->optionString($input, $name, '{}');
        if ('' === trim($raw)) {
            return [];
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function nonEmptyString(mixed $value, string $default = ''): string
    {
        $normalized = $this->scalarString($value);

        return '' !== $normalized ? $normalized : $default;
    }

    private function scalarString(mixed $value, string $default = ''): string
    {
        if (!is_scalar($value)) {
            return $default;
        }
        $normalized = trim((string) $value);

        return '' !== $normalized ? $normalized : $default;
    }

    /** @return array<string,mixed> */
    private function nestedMap(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    /** @return list<string> */
    private function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }
        $items = [];
        foreach ($value as $item) {
            if (!is_scalar($item)) {
                continue;
            }
            $normalized = trim((string) $item);
            if ('' !== $normalized) {
                $items[] = $normalized;
            }
        }

        return array_values($items);
    }

    /** @return array<string,string> */
    private function stringMap(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }
        $items = [];
        foreach ($value as $key => $item) {
            $normalizedKey = $this->scalarString($key);
            if ('' === $normalizedKey || !is_scalar($item)) {
                continue;
            }
            $items[$normalizedKey] = trim((string) $item);
        }

        return $items;
    }
}
