<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Controller;

use App\Ai\CategorySuggestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CategorySuggestController extends AbstractController
{
    public function __construct(private readonly CategorySuggestService $svc)
    {
    }

    #[Route('/api/category/suggest', name: 'api_category_suggest', methods: ['POST'])]
    public function suggest(Request $r): JsonResponse
    {
        $name = (string) $r->request->get('name', '');
        $desc = (string) $r->request->get('desc', '');
        $tags = $r->request->all('tag'); // tag[]=
        $res = $this->svc->suggest($name, $desc, is_array($tags) ? $tags : []);
        // simple audit log
        $logDir = getcwd().'/var/log';
        $this->ensureDirectory($logDir);
        $writer = new \App\Util\RotatingFileWriter($logDir.'/category_suggest.log');
        $writer->write(json_encode(['name' => $name, 'desc' => $desc, 'tags' => $tags, 'res' => $res])."\n");

        return $this->json(['ok' => true, 'item' => $res]);
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
