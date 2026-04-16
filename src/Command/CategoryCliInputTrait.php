<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\Service\CategoryPayloadValueNormalizer;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Provides shared helpers for category cli input trait.
 */
/** @noinspection DuplicatedCode */
trait CategoryCliInputTrait
{
    private function argumentString(InputInterface $input, string $name, string $default = ''): string
    {
        $value = $input->getArgument($name);

        return CategoryPayloadValueNormalizer::scalarString($value, $default);
    }

    private function optionString(InputInterface $input, string $name, string $default = ''): string
    {
        $value = $input->getOption($name);

        return CategoryPayloadValueNormalizer::scalarString($value, $default);
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

        return CategoryPayloadValueNormalizer::nestedMap(json_decode($raw, true));
    }

    private function nonEmptyString(mixed $value, string $default = ''): string
    {
        return CategoryPayloadValueNormalizer::nonEmptyString($value, $default);
    }

    /** @return array<string,mixed> */
    private function nestedMap(mixed $value): array
    {
        return CategoryPayloadValueNormalizer::nestedMap($value);
    }

    /** @return list<string> */
    private function stringList(mixed $value): array
    {
        return CategoryPayloadValueNormalizer::stringList($value);
    }

    /** @return array<string,string> */
    private function stringMap(mixed $value): array
    {
        return CategoryPayloadValueNormalizer::stringMap($value);
    }
}
