<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

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
    public function __construct(private readonly EntityManagerInterface $em, private readonly Connection $infra)
    {
    }

    #[Route('/api/category/{id}/pin', name: 'api_category_pin_create', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function pinCreate(string $id, Request $r): JsonResponse
    {
        $recordId = (string) $r->request->get('recordId');
        $pos = (int) $r->request->get('position', 0);
        $pin = new CategoryPin($id, $recordId, $pos);
        $this->em->persist($pin);
        $this->em->flush();

        return $this->json(['ok' => true]);
    }

    #[Route('/api/category/{id}/pin', name: 'api_category_pin_delete', methods: ['DELETE'])]
    #[IsGranted('category.merch')]
    public function pinDelete(string $id, Request $r): JsonResponse
    {
        $recordId = (string) $r->query->get('recordId');
        $pin = $this->em->getRepository(CategoryPin::class)->findOneBy(['categoryId' => $id, 'recordId' => $recordId]);
        if ($pin) {
            $this->em->remove($pin);
            $this->em->flush();
        }

        return $this->json(['ok' => true]);
    }

    #[Route('/api/category/{id}/order', name: 'api_category_order_set', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function orderSet(string $id, Request $r): JsonResponse
    {
        $list = $r->request->all('recordId'); // expect recordId[]=A&recordId[]=B ...
        $pos = 0;
        foreach ($list as $rid) {
            $this->em->getConnection()->executeStatement(
                'UPDATE category_pin SET position = ? WHERE category_id = ? AND record_id = ?',
                [$pos++, $id, $rid]
            );
        }

        return $this->json(['ok' => true]);
    }

    #[Route('/api/category/{id}/banner/publish', name: 'api_category_banner_publish', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function bannerPublish(string $id, Request $r): JsonResponse
    {
        $title = (string) $r->request->get('title');
        $content = (string) $r->request->get('content');
        $b = new CategoryBanner($id, $title, $content);
        $b->publish();
        $this->em->persist($b);
        $this->em->flush();

        return $this->json(['ok' => true, 'id' => $b->id()]);
    }

    #[Route('/api/category/{id}/html/publish', name: 'api_category_html_publish', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function htmlPublish(string $id, Request $r): JsonResponse
    {
        $html = (string) $r->request->get('html');
        $h = new CategoryHtmlBlock($id, $html);
        $h->publish();
        $this->em->persist($h);
        $this->em->flush();

        return $this->json(['ok' => true, 'id' => $h->id()]);
    }
}
