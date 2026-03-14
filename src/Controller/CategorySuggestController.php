<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Controller;

use App\Ai\CategorySuggestService;
use App\Util\RotatingFileWriter;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CategorySuggestController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly CategorySuggestService $service,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    #[Route('/api/catalog/suggest', name: 'api_category_suggest', methods: ['POST'])]
    public function suggest(Request $request): JsonResponse
    {
        $name = (string) $request->request->get('name', '');
        $description = (string) $request->request->get('desc', '');
        $tagList = $request->request->all('tag');

        try {
            $result = $this->service->suggest($name, $description, is_array($tagList) ? $tagList : []);

            $writer = new RotatingFileWriter(getcwd().'/var/log/category_suggest.log');
            $writer->write((string) json_encode([
                'name' => $name,
                'desc' => $description,
                'tags' => $tagList,
                'result' => $result,
            ], JSON_THROW_ON_ERROR)."\n");

            return $this->json([
                'ok' => true,
                'item' => $result,
                'message' => 'Category suggestions were generated successfully.',
            ]);
        } catch (\Throwable $throwable) {
            $this->logger->error('Category suggestion request failed.', [
                'name' => $name,
                'description' => $description,
                'tags' => $tagList,
                'exception' => $throwable,
            ]);

            return $this->json([
                'ok' => false,
                'error' => 'Category suggestions could not be generated.',
                'message' => 'Please review the request and try again. Check the logs if the problem continues.',
            ], 500);
        }
    }
}
