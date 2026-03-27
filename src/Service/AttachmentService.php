<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class AttachmentService
{
    private string $file = 'report/category-attachments.json';

    /** @return list<array{category_id:string,type:string,path:string}> */
    public function list(): array
    {
        if (!is_file($this->file)) {
            return [];
        }
        $json = file_get_contents($this->file);
        if (!is_string($json) || '' === $json) {
            return [];
        }
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, static function (mixed $row): bool {
            return is_array($row)
                && is_string($row['category_id'] ?? null)
                && is_string($row['type'] ?? null)
                && is_string($row['path'] ?? null);
        }));
    }

    public function add(string $categoryId, string $type, string $path): void
    {
        $all = $this->list();
        $all[] = ['category_id' => $categoryId, 'type' => $type, 'path' => $path];
        file_put_contents($this->file, json_encode($all, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    }
}
