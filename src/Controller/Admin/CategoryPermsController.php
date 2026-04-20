<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category perms controller application flow.
 */
final class CategoryPermsController extends AbstractController
{
    /**
     * Executes the invokable workflow for this service.
     */
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
