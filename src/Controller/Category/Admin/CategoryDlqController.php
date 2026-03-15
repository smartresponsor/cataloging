<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller\Category\Admin;

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
        $dlq = is_file($file) ? json_decode(file_get_contents($file), true) : [];
        if ($request->isMethod('POST')) {
            $id = (string) $request->request->get('id');
            $action = (string) $request->request->get('action');
            foreach ($dlq as &$msg) {
                if ($msg['id'] === $id) {
                    $msg['last_action'] = $action;
                    $msg['ts'] = date(DATE_ATOM);
                }
            }
            file_put_contents($file, json_encode($dlq, JSON_PRETTY_PRINT));
        }

        return $this->render('category/admin/dlq.html.twig', [
            'dlq' => $dlq,
        ]);
    }
}
