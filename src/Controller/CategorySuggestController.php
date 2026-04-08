<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Ai\CatalogSuggestService;
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
    public function __construct(private readonly CatalogSuggestService $svc)
    {
    }
    /**
     * Handles the suggest workflow.
     */
    #[Route('/api/category/suggest', name: 'api_category_suggest', methods: ['POST'])]
    public function suggest(Request $r): JsonResponse
    {
        $name = $this->requestString($r, 'name');
        $desc = $this->requestString($r, 'desc');
        $tags = $this->requestStringList($r, 'tag');
        $res = $this->svc->suggest($name, $desc, $tags);
        // simple audit log
        $logDir = getcwd().'/var/log';
        $this->ensureDirectory($logDir);
        $writer = new \App\Util\RotatingFileWriter($logDir.'/category_suggest.log');
        $writer->write(json_encode(['name' => $name, 'desc' => $desc, 'tags' => $tags, 'res' => $res], JSON_THROW_ON_ERROR)."\n");

        return $this->json(['ok' => true, 'item' => $res]);
    }

    private function requestString(Request $request, string $key): string
    {
        $value = $request->request->get($key, '');

        return is_scalar($value) ? trim((string) $value) : '';
    }

    /** @return list<string> */
    private function requestStringList(Request $request, string $key): array
    {
        $values = $request->request->all($key);
        if (!is_array($values)) {
            return [];
        }

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
