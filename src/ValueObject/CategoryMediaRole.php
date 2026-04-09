<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryMediaRoleInterface;
/**
 * Represents the category media role value.
 */
final class CategoryMediaRole implements CategoryMediaRoleInterface
{
private const string PRIMARY = 'primary';
private const string BANNER = 'banner';
private const string ICON = 'icon';
private const string THUMBNAIL = 'thumbnail';
private const string HERO = 'hero';
    private function __construct(private readonly string $value)
    {
    }

    public static function primary(): self
    {
        return new self(self::PRIMARY);
    }

    public static function banner(): self
    {
        return new self(self::BANNER);
    }

    public static function icon(): self
    {
        return new self(self::ICON);
    }

    public static function thumbnail(): self
    {
        return new self(self::THUMBNAIL);
    }

    public static function hero(): self
    {
        return new self(self::HERO);
    }

    public static function fromString(string $value): self
    {
        return match ($value) {
            self::PRIMARY => self::primary(),
            self::BANNER => self::banner(),
            self::ICON => self::icon(),
            self::THUMBNAIL => self::thumbnail(),
            self::HERO => self::hero(),
            default => throw new \InvalidArgumentException(sprintf('Unsupported category media role "%s".', $value)),
        };
    }
    /**
     * Handles the value workflow.
     */
    public function value(): string
    {
        return $this->value;
    }
}
