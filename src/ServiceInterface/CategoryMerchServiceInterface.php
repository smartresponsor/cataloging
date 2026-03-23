<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

interface CategoryMerchServiceInterface
{
    public function pinCreate(string $categoryId, string $recordId, int $position): void;

    public function pinDelete(string $categoryId, string $recordId): void;

    /**
     * @param list<string> $recordIds
     */
    public function orderSet(string $categoryId, array $recordIds): void;

    public function bannerPublish(string $categoryId, string $title, string $content): string;

    public function htmlPublish(string $categoryId, string $html): string;
}
