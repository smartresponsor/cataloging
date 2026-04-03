<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SearchService;
use App\ServiceInterface\Security\SecurityExternalIdentityContextResolverInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CategorySearchController
{
    public function __construct(
        private readonly SearchService $search,
        private readonly SecurityExternalIdentityContextResolverInterface $externalIdentityContextResolver,
        private readonly Security $security,
    ) {
    }

    #[Route('/api/category/search', name: 'api_category_search', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $criteria = [
                'q' => $request->query->get('q'),
                'tenant' => $request->query->get('tenant'),
                'locale' => $request->query->get('locale'),
                'workflow_state' => $request->query->get('workflow_state'),
                'published' => $request->query->get('published'),
                'limit' => $request->query->get('limit'),
                'offset' => $request->query->get('offset'),
                'order' => $request->query->get('order'),
                'direction' => $request->query->get('direction'),
            ];

            $criteria = $this->applyTenantScope($request, $criteria);
            $result = $this->search->search($criteria);

            return new JsonResponse($result);
        } catch (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 403);
        }
    }

    /**
     * @param array<string,mixed> $criteria
     * @return array<string,mixed>
     */
    private function applyTenantScope(Request $request, array $criteria): array
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $criteria;
        }

        $context = $this->externalIdentityContextResolver->resolveFromRequest($request);
        if (null === $context || null === $context->tenant) {
            $criteria['published'] ??= true;

            return $criteria;
        }

        $requestedTenant = is_scalar($criteria['tenant'] ?? null) ? trim((string) $criteria['tenant']) : '';
        if ('' !== $requestedTenant && $requestedTenant !== $context->tenant) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Cross-tenant category search is not allowed for the current actor.');
        }

        $criteria['tenant'] = $context->tenant;

        return $criteria;
    }
}
