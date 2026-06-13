<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

/** CategoryEntity taxonomy entity (code: product, project, vendor, report, etc.). */
final class CatalogTaxonomyEntity
{
    /** @var string ULID */
    private string $id;
    private string $code;
    /** @var array<string,string> */
    private array $nameEntity;
    /** @var array<string,mixed> */
    private array $rule;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    /**
     * @param array<string,string> $nameEntity
     * @param array<string,mixed>  $rule
     */
    public function __construct(
        string $id,
        string $code,
        array $nameEntity,
        array $rule,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->nameEntity = $nameEntity;
        $this->rule = $rule;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * Handles the id workflow.
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Handles the code workflow.
     */
    public function code(): string
    {
        return $this->code;
    }

    /** @return array<string,string> */
    public function nameEntity(): array
    {
        return $this->nameEntity;
    }

    /** @return array<string,mixed> */
    public function rule(): array
    {
        return $this->rule;
    }

    /**
     * Creates the d at result for the current workflow.
     */
    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Handles the updated at workflow.
     */
    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
