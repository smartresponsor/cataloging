<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Ai\CatalogSuggestService;
use App\Util\RotatingFileWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category suggest controller application flow.
 */
final class CategorySuggestController extends AbstractController
{
    /**
     * Initializes the category suggest controller service collaborators.
     */
    public function __construct(private readonly CatalogSuggestService $catalogSuggestService)
    {
    }

    /**
     * Handles the suggest workflow.
     */
    #[Route('/api/category/suggest', name: 'api_category_suggest', methods: ['POST'])]
    public function suggest(Request $request): JsonResponse
    {
        try {
            $name = $this->requestString($request, 'name');
            $description = $this->requestString($request, 'desc');
            $tags = $this->requestTags($request);
            $suggestion = $this->catalogSuggestService->suggest($name, $description, $tags);

            $logDir = getcwd().'/var/log';
            $this->ensureDirectory($logDir);
            $writer = new RotatingFileWriter($logDir.'/category_suggest.log');
            $writer->write(
                json_encode(
                    ['name' => $name, 'desc' => $description, 'tags' => $tags, 'res' => $suggestion],
                    JSON_THROW_ON_ERROR,
                )."\n",
            );

            return $this->json(['ok' => true, 'item' => $suggestion]);
        } catch (\Throwable) {
            return $this->json(['ok' => false, 'error' => 'category_suggest_failed'], 500);
        }
    }

    private function requestString(Request $request, string $key): string
    {
        $value = $request->request->get($key, '');

        return is_scalar($value) ? trim((string) $value) : '';
    }

    /** @return list<string> */
    private function requestTags(Request $request): array
    {
        $values = $request->request->all('tag');

        $result = [];
        foreach ($values as $value) {
            if (!is_scalar($value)) {
                continue;
            }

            $normalized = trim((string) $value);
            if ('' !== $normalized) {
                $result[] = $normalized;
            }
        }

        return array_values($result);
    }

    private function ensureDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0o755, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Unable to create directory: %s', $path));
        }
    }
}
