<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryDlqController extends AbstractController
{
    #[Route('/admin/category/dlq', name: 'admin_category_dlq', methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        $file = 'report/category-dlq.json';
        $dlq = $this->readDlq($file);

        if ($request->isMethod('POST')) {
            $id = (string) $request->request->get('id');
            $action = (string) $request->request->get('action');
            foreach ($dlq as &$message) {
                if ($message['id'] !== $id) {
                    continue;
                }

                $message['last_action'] = $action;
                $message['ts'] = date(DATE_ATOM);
            }
            unset($message);

            file_put_contents($file, json_encode($dlq, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return $this->render('category/admin/dlq.html.twig', [
            'dlq' => $dlq,
        ]);
    }

    /** @return list<array{id:string,last_action?:string,ts?:string}> */
    private function readDlq(string $file): array
    {
        if (!is_file($file) || !is_readable($file)) {
            return [];
        }

        $content = file_get_contents($file);
        if (false === $content || '' === trim($content)) {
            return [];
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return [];
        }

        $result = [];
        foreach ($decoded as $row) {
            if (!is_array($row)) {
                continue;
            }
            $result[] = [
                'id' => is_scalar($row['id'] ?? null) ? (string) $row['id'] : '',
                'last_action' => is_scalar($row['last_action'] ?? null) ? (string) $row['last_action'] : '',
                'ts' => is_scalar($row['ts'] ?? null) ? (string) $row['ts'] : '',
            ];
        }

        return $result;
    }
}
