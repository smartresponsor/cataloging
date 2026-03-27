<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryPublicationReadinessInterface;

final class CategoryPublicationReadiness implements CategoryPublicationReadinessInterface
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $blockers
     * @param list<string>       $warnings
     */
    public function __construct(
        private readonly array $checks,
        private readonly array $blockers,
        private readonly array $warnings = [],
    ) {
    }

    /** @param array<string,bool> $checks */
    public static function fromChecks(array $checks): self
    {
        $required = [
            'slugReady',
            'seoReady',
            'contentReady',
            'localeReady',
        ];

        $normalized = [];
        foreach ($checks as $name => $value) {
            $normalized[(string) $name] = (bool) $value;
        }

        $blockers = [];
        foreach ($required as $name) {
            if (($normalized[$name] ?? false) !== true) {
                $blockers[] = $name;
            }
        }

        $warnings = [];
        if (($normalized['mediaReady'] ?? false) !== true) {
            $warnings[] = 'mediaReady';
        }
        if (($normalized['aliasReady'] ?? false) !== true) {
            $warnings[] = 'aliasReady';
        }

        return new self($normalized, $blockers, $warnings);
    }

    public function isPublishable(): bool
    {
        return [] === $this->blockers;
    }

    public function hasCheck(string $name): bool
    {
        return array_key_exists($name, $this->checks);
    }

    public function check(string $name): bool
    {
        return ($this->checks[$name] ?? false) === true;
    }

    public function blockers(): array
    {
        return $this->blockers;
    }

    public function warnings(): array
    {
        return $this->warnings;
    }

    public function checks(): array
    {
        return $this->checks;
    }
}
