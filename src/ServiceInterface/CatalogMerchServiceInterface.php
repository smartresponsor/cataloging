<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\ValueObject\CategoryMerchBannerPublishRequest;
use App\ValueObject\CategoryMerchPinCreateRequest;

/**
 * Defines the contract for catalog merch service.
 */
interface CatalogMerchServiceInterface
{
    /**
     * Handles the pin create workflow.
     */
    public function pinCreate(CategoryMerchPinCreateRequest $request): void;
    /**
     * Handles the pin delete workflow.
     */
    public function pinDelete(string $categoryId, string $recordId): void;

    /**
     * @param list<string> $recordIds
     */
    public function orderSet(string $categoryId, array $recordIds): void;
    /**
     * Handles the banner publish workflow.
     */
    public function bannerPublish(CategoryMerchBannerPublishRequest $request): string;
    /**
     * Handles the html publish workflow.
     */
    public function htmlPublish(string $categoryId, string $html): string;
}
