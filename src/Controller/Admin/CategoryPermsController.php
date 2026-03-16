<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryPermsController extends AbstractController
{
    #[Route('/admin/category/perms', name: 'admin_category_perms')]
    public function __invoke(): Response
    {
        $perms = [
            ['role' => 'ROLE_ADMIN', 'create' => true, 'move' => true, 'publish' => true],
            ['role' => 'ROLE_MERCHANT', 'create' => false, 'move' => false, 'publish' => false],
        ];

        return $this->render('category/admin/perms.html.twig', ['perms' => $perms]);
    }
}
