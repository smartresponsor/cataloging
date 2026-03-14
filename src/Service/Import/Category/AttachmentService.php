<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Import\Category;

final class AttachmentService
{
    private string $file = 'report/catalog-attachments.json';

    public function list(): array
    {
        if (!is_file($this->file)) {
            return [];
        }

        return json_decode(file_get_contents($this->file), true) ?? [];
    }

    public function add(string $categoryId, string $type, string $path): void
    {
        $all = $this->list();
        $all[] = [
            'category_id' => $categoryId,
            'type' => $type,
            'path' => $path,
        ];
        file_put_contents($this->file, json_encode($all, JSON_PRETTY_PRINT));
    }
}
