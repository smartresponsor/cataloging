<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\CategoryBanner;
use App\Entity\CategoryHtmlBlock;
use App\Entity\CategoryPin;
use App\ServiceInterface\CatalogMerchServiceInterface;
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
     * @param string       $categoryId
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
    public function pinCreate(string $categoryId, string $recordId, int $position): void
    {
        $pin = new CategoryPin($categoryId, $recordId, $position);
        $this->entityManager->persist($pin);
        $this->entityManager->flush();
    }

    /**
     * Handles the pin delete workflow.
     */
    public function pinDelete(string $categoryId, string $recordId): void
    {
        $pin = $this->entityManager->getRepository(CategoryPin::class)->findOneBy([
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
    public function bannerPublish(string $categoryId, string $title, string $content): string
    {
        $banner = new CategoryBanner($categoryId, $title, $content);
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
        $htmlBlock = new CategoryHtmlBlock($categoryId, $html);
        $htmlBlock->publish();
        $this->entityManager->persist($htmlBlock);
        $this->entityManager->flush();

        return (string) $htmlBlock->id();
    }
}
