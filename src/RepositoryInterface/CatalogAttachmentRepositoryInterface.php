<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\RepositoryInterface;

interface CatalogAttachmentRepositoryInterface
{
    /**
     * @return list<array{attachment_id:string,category_id:string,type:string,path:string,created_at:string}>
     */
    public function list(?string $categoryId = null): array;

    /**
     * @return array{attachment_id:string,category_id:string,type:string,path:string,created_at:string}
     */
    public function add(string $categoryId, string $type, string $path): array;

    public function delete(string $attachmentId): bool;
}
