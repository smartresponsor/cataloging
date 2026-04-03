<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\RepositoryInterface;

interface CatalogAttachmentRepositoryInterface
{
    /**
     * @return list<array{attachment_id:string,category_id:string,type:string,provider:string,external_attachment_id:string,reference_uri:?string,path:?string,created_at:string}>
     */
    public function list(?string $categoryId = null): array;

    /**
     * @return array{attachment_id:string,category_id:string,type:string,provider:string,external_attachment_id:string,reference_uri:?string,path:?string,created_at:string}
     */
    public function add(string $categoryId, string $type, string $provider, string $externalAttachmentId, ?string $referenceUri = null): array;

    /**
     * @return array{attachment_id:string,category_id:string,type:string,provider:string,external_attachment_id:string,reference_uri:?string,path:?string,created_at:string}|null
     */
    public function findOne(string $attachmentId): ?array;

    public function delete(string $attachmentId): bool;
}
