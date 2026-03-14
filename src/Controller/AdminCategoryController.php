<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CategoryEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class AdminCategoryController extends AbstractController
{
    #[Route('/admin/category/preview-move', name: 'admin_category_preview_move', methods: ['POST'])]
    public function previewMove(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $sourceId = (string) $request->request->get('sourceId');
        $targetId = (string) $request->request->get('targetParentId');

        /** @var CategoryEntity|null $source */
        $source = $entityManager->getRepository(CategoryEntity::class)->find($sourceId);
        /** @var CategoryEntity|null $target */
        $target = $entityManager->getRepository(CategoryEntity::class)->find($targetId);

        if (null === $source || null === $target) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        $leaf = basename(str_replace('.', '/', $source->getPath()));
        $newPath = $target->getPath().'.'.$leaf;
        $newDepth = $target->getDepth() + 1;
        $duplicate = $entityManager->getRepository(CategoryEntity::class)->findOneBy([
            'slug' => $source->getSlug(),
            'depth' => $newDepth,
        ]);

        return $this->json([
            'ok' => true,
            'preview' => [
                'newPath' => $newPath,
                'newDepth' => $newDepth,
                'conflict' => null !== $duplicate,
            ],
        ]);
    }
}
