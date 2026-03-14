<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CategoryBanner;
use App\Entity\CategoryHtmlBlock;
use App\Entity\CategoryPin;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CategoryMerchController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Connection $connection,
    ) {
    }

    #[Route('/api/catalog/{id}/pin', name: 'api_category_pin_create', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function pinCreate(string $id, Request $request): JsonResponse
    {
        $recordId = (string) $request->request->get('recordId');
        $position = (int) $request->request->get('position', 0);
        $pin = new CategoryPin($id, $recordId, $position);
        $this->entityManager->persist($pin);
        $this->entityManager->flush();

        return $this->json(['ok' => true]);
    }

    #[Route('/api/catalog/{id}/pin', name: 'api_category_pin_delete', methods: ['DELETE'])]
    #[IsGranted('category.merch')]
    public function pinDelete(string $id, Request $request): JsonResponse
    {
        $recordId = (string) $request->query->get('recordId');
        $pin = $this->entityManager->getRepository(CategoryPin::class)->findOneBy([
            'categoryId' => $id,
            'recordId' => $recordId,
        ]);
        if (null !== $pin) {
            $this->entityManager->remove($pin);
            $this->entityManager->flush();
        }

        return $this->json(['ok' => true]);
    }

    #[Route('/api/catalog/{id}/order', name: 'api_category_order_set', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function orderSet(string $id, Request $request): JsonResponse
    {
        $recordIdList = $request->request->all('recordId');
        $position = 0;
        foreach ($recordIdList as $recordId) {
            $this->connection->executeStatement(
                'UPDATE category_pin SET position = ? WHERE category_id = ? AND record_id = ?',
                [$position++, $id, (string) $recordId],
            );
        }

        return $this->json(['ok' => true]);
    }

    #[Route('/api/catalog/{id}/banner/publish', name: 'api_category_banner_publish', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function bannerPublish(string $id, Request $request): JsonResponse
    {
        $title = (string) $request->request->get('title');
        $content = (string) $request->request->get('content');
        $banner = new CategoryBanner($id, $title, $content);
        $banner->publish();
        $this->entityManager->persist($banner);
        $this->entityManager->flush();

        return $this->json(['ok' => true, 'item' => ['bannerId' => $banner->getId()]]);
    }

    #[Route('/api/catalog/{id}/html', name: 'api_category_html_set', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function htmlSet(string $id, Request $request): JsonResponse
    {
        $slot = (string) $request->request->get('slot', 'default');
        $html = (string) $request->request->get('html', '');
        $block = $this->entityManager->getRepository(CategoryHtmlBlock::class)->findOneBy([
            'categoryId' => $id,
            'slot' => $slot,
        ]);
        if (null === $block) {
            $block = new CategoryHtmlBlock($id, $slot, $html);
            $this->entityManager->persist($block);
        } else {
            $block->setHtml($html);
        }
        $this->entityManager->flush();

        return $this->json(['ok' => true]);
    }
}
