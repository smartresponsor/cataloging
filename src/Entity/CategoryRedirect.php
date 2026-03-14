<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\CategoryRedirectInterface;
use Doctrine\ORM\Mapping as ORM;

/** Redirect record from old full slug to new one (SEO). */
#[ORM\Entity]
#[ORM\Table(name: 'category_redirect')]
#[ORM\UniqueConstraint(name: 'uniq_category_redirect_from', columns: ['from_path'])]
final class CategoryRedirect implements CategoryRedirectInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 26, options: ['fixed' => true])]
    private string $id;

    #[ORM\Column(name: 'from_path', type: 'string', length: 255)]
    private string $from;

    #[ORM\Column(name: 'to_path', type: 'string', length: 255)]
    private string $to;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $id, string $from, string $to, ?\DateTimeImmutable $createdAt = null)
    {
        $this->id = $id;
        $this->from = $from;
        $this->to = $to;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable('now');
    }

    public function id(): string
    {
        return $this->id;
    }

    public function frm(): string
    {
        return $this->from;
    }

    public function to(): string
    {
        return $this->to;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
