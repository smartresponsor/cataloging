<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller\Category\Merchant;

use App\Service\TenantFilter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryMerchantController extends AbstractController
{
    public function __construct(private readonly TenantFilter $tenantFilter)
    {
    }

    #[Route('/merchant/category', name: 'merchant_category_index')]
    public function index(): Response
    {
        $all = [
            ['id' => 1, 'name' => 'Root', 'tenant' => 'default'],
            ['id' => 2, 'name' => 'Merchant Only', 'tenant' => 'merchant'],
        ];
        $filtered = $this->tenantFilter->filter($all, 'merchant');

        return $this->render('category/merchant/list.html.twig', [
            'categories' => $filtered,
        ]);
    }
}
