<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents the projection control entity domain record.
 */
#[ORM\Entity]
#[ORM\Table(name: 'projection_control')]
class ProjectionControlEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    private string $id = 'category';

    #[ORM\Column(type: 'boolean')]
    private bool $paused = false;

    /**
     * Handles the id workflow.
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Handles the paused workflow.
     */
    public function paused(): bool
    {
        return $this->paused;
    }

    /**
     * Updates the paused value.
     */
    public function setPaused(bool $p): void
    {
        $this->paused = $p;
    }
}
