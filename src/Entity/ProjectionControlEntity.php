<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'projection_control')]
final class ProjectionControlEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    private string $id;

    #[ORM\Column(type: 'boolean')]
    private bool $paused = false;

    public function __construct(string $id = 'category', bool $paused = false)
    {
        $this->id = $id;
        $this->paused = $paused;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function paused(): bool
    {
        return $this->paused;
    }

    public function setPaused(bool $p): void
    {
        $this->paused = $p;
    }
}
