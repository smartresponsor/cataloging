<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CatalogCategoryBannerEntity;
use App\Cataloging\Entity\CatalogCategoryHtmlBlockEntity;
use App\Cataloging\Entity\CatalogCategoryPinEntity;
use App\Cataloging\ServiceInterface\CatalogMerchServiceInterface;
use App\Cataloging\ValueObject\CategoryMerchBannerPublishRequest;
use App\Cataloging\ValueObject\CategoryMerchPinCreateRequest;
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
     */
    public function orderSet(string $categoryId, array $recordIds): void
    {
        $position = 0;
        $repository = $this->entityManager->getRepository(CatalogCategoryPinEntity::class);

        foreach ($recordIds as $recordId) {
            $pin = $repository->findOneBy([
                'categoryId' => $categoryId,
                'recordId' => $recordId,
            ]);

            if (!$pin instanceof CatalogCategoryPinEntity) {
                continue;
            }

            $pin->setPosition($position++);
        }

        $this->entityManager->flush();
    }

    /**
     * Handles the pin create workflow.
     */
    public function pinCreate(CategoryMerchPinCreateRequest $request): void
    {
        $pin = new CatalogCategoryPinEntity($request->categoryId, $request->recordId, $request->position);
        $this->entityManager->persist($pin);
        $this->entityManager->flush();
    }

    /**
     * Handles the pin delete workflow.
     */
    public function pinDelete(string $categoryId, string $recordId): void
    {
        $pin = $this->entityManager->getRepository(CatalogCategoryPinEntity::class)->findOneBy([
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
        $banner = new CatalogCategoryBannerEntity($request->categoryId, $request->title, $request->content);
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
        $htmlBlock = new CatalogCategoryHtmlBlockEntity($categoryId, $html);
        $htmlBlock->publish();
        $this->entityManager->persist($htmlBlock);
        $this->entityManager->flush();

        return (string) $htmlBlock->id();
    }
}
