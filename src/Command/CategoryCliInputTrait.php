<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Command;

use App\Cataloging\Service\CategoryPayloadValueNormalizer;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Provides shared helpers for category cli input trait.
 */
/** @noinspection DuplicatedCode */
trait CategoryCliInputTrait
{
    private function argumentString(InputInterface $input, string $nameEntity, string $default = ''): string
    {
        $value = $input->getArgument($nameEntity);

        return CategoryPayloadValueNormalizer::scalarString($value, $default);
    }

    private function optionString(InputInterface $input, string $nameEntity, string $default = ''): string
    {
        $value = $input->getOption($nameEntity);

        return CategoryPayloadValueNormalizer::scalarString($value, $default);
    }

    private function argumentInt(InputInterface $input, string $nameEntity, int $default = 0): int
    {
        $value = $input->getArgument($nameEntity);

        return is_numeric($value) ? (int) $value : $default;
    }

    /** @return array<string,mixed> */
    private function jsonOptionMap(InputInterface $input, string $nameEntity): array
    {
        $raw = $this->optionString($input, $nameEntity, '{}');
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
