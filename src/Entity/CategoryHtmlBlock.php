<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'category_html_block')]
class CategoryHtmlBlock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\Column(type: 'string', length: 26)]
    private string $categoryId;

    #[ORM\Column(type: 'text')]
    private string $html;

    #[ORM\Column(type: 'boolean')]
    private bool $isDraft = true;

    public function __construct(string $categoryId, string $html)
    {
        $this->categoryId = $categoryId;
        $this->html = $html;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function html(): string
    {
        return $this->html;
    }

    public function isDraft(): bool
    {
        return $this->isDraft;
    }

    public function publish(): void
    {
        $this->isDraft = false;
    }
}
