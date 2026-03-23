<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'category_banner')]
class CategoryBanner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 26)]
    private string $categoryId;

    #[ORM\Column(type: 'string', length: 160)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'boolean')]
    private bool $isDraft = true;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    public function __construct(string $categoryId, string $title, string $content)
    {
        $this->categoryId = $categoryId;
        $this->title = $title;
        $this->content = $content;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function isDraft(): bool
    {
        return $this->isDraft;
    }

    public function publish(): void
    {
        $this->isDraft = false;
        $this->publishedAt = new \DateTimeImmutable('now');
    }
}
