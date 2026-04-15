<?php

declare(strict_types=1);

namespace App\Tests\Request\Support;

use App\Request\Support\RequestValueNormalizer;
use PHPUnit\Framework\TestCase;

final class RequestValueNormalizerTest extends TestCase
{
    public function testTrimmedStringOrDefault(): void
    {
        self::assertSame('default', RequestValueNormalizer::trimmedStringOrDefault(null, 'default'));
        self::assertSame('default', RequestValueNormalizer::trimmedStringOrDefault('   ', 'default'));
        self::assertSame('abc', RequestValueNormalizer::trimmedStringOrDefault(' abc ', 'default'));
    }

    public function testOptionalTrimmedString(): void
    {
        self::assertNull(RequestValueNormalizer::optionalTrimmedString(null));
        self::assertNull(RequestValueNormalizer::optionalTrimmedString(' '));
        self::assertSame('x', RequestValueNormalizer::optionalTrimmedString(' x '));
    }

    public function testBoolFromMixed(): void
    {
        self::assertTrue(RequestValueNormalizer::boolFromMixed('true'));
        self::assertFalse(RequestValueNormalizer::boolFromMixed('not-bool', false));
        self::assertTrue(RequestValueNormalizer::boolFromMixed(1));
    }

    public function testNullableBoolFromMixed(): void
    {
        self::assertTrue(RequestValueNormalizer::nullableBoolFromMixed('1'));
        self::assertFalse(RequestValueNormalizer::nullableBoolFromMixed(0));
        self::assertNull(RequestValueNormalizer::nullableBoolFromMixed('unknown'));
    }
}
