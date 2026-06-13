<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\ServiceInterface\CatalogCategoryProjectionReadServiceInterface;
use App\Cataloging\ServiceInterface\Security\SecurityExternalIdentityContextResolverInterface;
use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
/**
 * Merchant delivery adapter over shared projection-backed catalog services.
 */
final class CatalogCategoryMerchantController extends AbstractController
{
    /**
     * Initializes the category merchant controller service collaborators.
     */
    public function __construct(
        private readonly CatalogCategoryProjectionReadServiceInterface $categoryProjectionReadService,
        private readonly SecurityExternalIdentityContextResolverInterface $externalIdentityContextResolver,
    ) {
    }

    /**
     * Handles the index workflow.
     */
    #[Route('/merchant/category', name: 'merchant_category_index')]
    public function index(Request $request): array
    {
        try {
            $context = $this->externalIdentityContextResolver->resolveFromRequest($request);
            $tenant = $context->tenant ?? 'merchant';
            $categories = $this->categoryProjectionReadService->list(CategoryProjectionCriteria::fromArray([
                'tenant' => $tenant,
                'limit' => 100,
                'offset' => 0,
                'order' => 'nameEntity',
                'direction' => 'asc',
            ]));
        } catch (\Throwable $exception) {
            throw $this->createNotFoundException('Unable to load merchant categories.', $exception);
        }

        return [
            '_view' => [
                'surface' => 'category',
                'operation' => 'merchant-index',
                'intent' => 'merchant',
                'component' => 'Cataloging',
                'format' => 'auto',
            ],
            'locations' => ['body' => ['categories' => $categories]],
            'data' => ['categories' => $categories],
            'meta' => ['source' => 'catalog_category_merchant_controller'],
        ];
    }
}
