<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller\Merchant;

use App\ServiceInterface\CategoryProjectionReadServiceInterface;
use App\ServiceInterface\Security\SecurityExternalIdentityContextResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
/**
 * Merchant delivery adapter over shared projection-backed catalog services.
 */
final class CategoryMerchantController extends AbstractController
{
    /**
     * Initializes the category merchant controller service collaborators.
     */
    public function __construct(
        private readonly CategoryProjectionReadServiceInterface $categoryProjectionReadService,
        private readonly SecurityExternalIdentityContextResolverInterface $externalIdentityContextResolver,
    ) {
    }
    /**
     * Handles the index workflow.
     */
    #[Route('/merchant/category', name: 'merchant_category_index')]
    public function index(Request $request): Response
    {
        $context = $this->externalIdentityContextResolver->resolveFromRequest($request);
        $tenant = $context?->tenant ?? 'merchant';
        $categories = $this->categoryProjectionReadService->list([
            'tenant' => $tenant,
            'limit' => 100,
            'offset' => 0,
            'order' => 'name',
            'direction' => 'asc',
        ]);

        return $this->render('category/merchant/list.html.twig', [
            'categories' => $categories,
        ]);
    }
}
