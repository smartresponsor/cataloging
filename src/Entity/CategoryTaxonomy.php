<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

/** Category taxonomy entity (code: product, project, vendor, report, etc.). */
final class CategoryTaxonomy
{
    /** @var string ULID */
    private string $id;
    private string $code;
    /** @var array<string,string> */
    private array $name;
    /** @var array<string,mixed> */
    private array $rule;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    /**
     * @param array<string,string> $name
     * @param array<string,mixed>  $rule
     */
    public function __construct(
        string $id,
        string $code,
        array $name,
        array $rule,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
        $this->rule = $rule;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function code(): string
    {
        return $this->code;
    }

    /** @return array<string,string> */
    public function name(): array
    {
        return $this->name;
    }

    /** @return array<string,mixed> */
    public function rule(): array
    {
        return $this->rule;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
