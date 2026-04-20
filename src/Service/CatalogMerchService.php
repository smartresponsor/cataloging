<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CatalogCategoryBanner;
use App\Cataloging\Entity\CatalogCategoryHtmlBlock;
use App\Cataloging\Entity\CatalogCategoryPin;
use App\Cataloging\ServiceInterface\CatalogMerchServiceInterface;
use App\Cataloging\ValueObject\CategoryMerchBannerPublishRequest;
use App\Cataloging\ValueObject\CategoryMerchPinCreateRequest;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides the catalog merch service application service.
 */
final readonly class CatalogMerchService implements CatalogMerchServiceInterface
{
    /**
     * Initializes the catalog merch service service collaborators.
     */
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param list<string> $recordIds
     *
     * @throws Exception
     */
    public function orderSet(string $categoryId, array $recordIds): void
    {
        $position = 0;
        foreach ($recordIds as $recordId) {
            $this->entityManager->getConnection()->executeStatement(
                'UPDATE category_pin SET position = ? WHERE category_id = ? AND record_id = ?',
                [$position++, $categoryId, $recordId],
            );
        }
    }

    /**
     * Handles the pin create workflow.
     */
    public function pinCreate(CategoryMerchPinCreateRequest $request): void
    {
        $pin = new CatalogCategoryPin($request->categoryId, $request->recordId, $request->position);
        $this->entityManager->persist($pin);
        $this->entityManager->flush();
    }

    /**
     * Handles the pin delete workflow.
     */
    public function pinDelete(string $categoryId, string $recordId): void
    {
        $pin = $this->entityManager->getRepository(CatalogCategoryPin::class)->findOneBy([
            'categoryId' => $categoryId,
            'recordId' => $recordId,
        ]);

        if (null === $pin) {
            return;
        }

        $this->entityManager->remove($pin);
        $this->entityManager->flush();
    }

    /**
     * Handles the banner publish workflow.
     */
    public function bannerPublish(CategoryMerchBannerPublishRequest $request): string
    {
        $banner = new CatalogCategoryBanner($request->categoryId, $request->title, $request->content);
        $banner->publish();
        $this->entityManager->persist($banner);
        $this->entityManager->flush();

        return (string) $banner->id();
    }

    /**
     * Handles the html publish workflow.
     */
    public function htmlPublish(string $categoryId, string $html): string
    {
        $htmlBlock = new CatalogCategoryHtmlBlock($categoryId, $html);
        $htmlBlock->publish();
        $this->entityManager->persist($htmlBlock);
        $this->entityManager->flush();

        return (string) $htmlBlock->id();
    }
}
