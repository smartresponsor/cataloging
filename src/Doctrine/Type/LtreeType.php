<?php

declare(strict_types=1);

namespace App\Cataloging\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class LtreeType extends Type
{
    public const NAME = 'ltree';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'ltree';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        return is_scalar($value) ? (string) $value : null;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        return is_scalar($value) ? (string) $value : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return false;
    }
}
