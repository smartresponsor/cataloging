<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\RepositoryInterface\CatalogCollectionProjectionRepositoryInterface;
use App\Service\CatalogLegacyCollectionFilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
/**
 * Handles the legacy collection list controller application flow.
 */
final class LegacyCollectionListController extends AbstractController
{
    /**
     * Initializes the legacy collection list controller service collaborators.
     */
    public function __construct(
        private readonly CatalogLegacyCollectionFilterService $service,
        private readonly CatalogCollectionProjectionRepositoryInterface $projectionRepository,
    ) {
    }
    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/collection/list', name: 'legacy_collection_list_handler', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $rule = trim((string) $request->query->get('rule', ''));
        $products = $this->mapProjectionRows($this->projectionRepository->list());
        $filtered = '' !== $rule ? $this->service->filter($products, $rule) : $products;

        return $this->json([
            'items' => $filtered,
            'total' => [
                'value' => count($filtered),
                'accuracy' => 'exact',
            ],
        ]);
    }

    /**
     * @param list<array<string, list<bool|float|int|string>|bool|float|int|string|null>> $rows
     *
     * @return list<array<string,mixed>>
     */
    private function mapProjectionRows(array $rows): array
    {
        $products = [];
        foreach ($rows as $row) {
            $products[] = [
                'id' => is_scalar($row['id'] ?? null) ? (string) $row['id'] : '',
                'price' => is_numeric($row['price'] ?? null) ? (float) $row['price'] : 0.0,
                'tags' => $this->filterTags($row),
            ];
        }

        return $products;
    }

    /**
     * @param array<string,mixed> $row
     *
     * @return list<string>
     */
    private function filterTags(array $row): array
    {
        $tagSet = $row['tag_set'] ?? null;
        if (!is_array($tagSet)) {
            return [];
        }

        $tags = array_filter(
            $tagSet,
            static fn (mixed $value): bool =>
                is_bool($value)
                || is_float($value)
                || is_int($value)
                || is_string($value),
        );

        return array_values($tags);
    }

}
