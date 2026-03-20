<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

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
    public function previewMove(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $sourceId = (string) $req->request->get('sourceId');
        $targetId = (string) $req->request->get('targetParentId');
        /** @var CategoryEntity|null $source */
        $source = $em->getRepository(CategoryEntity::class)->find($sourceId);
        /** @var CategoryEntity|null $target */
        $target = $em->getRepository(CategoryEntity::class)->find($targetId);
        if (!$source || !$target) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }
        // New path and depth
        $newPath = $target->getPath().'.'.basename(str_replace('.', '/', $source->getPath()));
        $newDepth = $target->getDepth() + 1;
        // Check conflict by slug under new parent
        $dup = $em->getRepository(CategoryEntity::class)->findOneBy(['slug' => $source->getSlug(), 'depth' => $newDepth]);
        $conflict = $dup ? true : false;

        return $this->json([
            'ok' => true,
            'preview' => ['newPath' => $newPath, 'newDepth' => $newDepth, 'conflict' => $conflict],
        ]);
    }
}
