<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Http\Category;

use App\Service\Category\WebhookDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class WebhookController
{
    public function __construct(private readonly WebhookDispatcher $dispatcher)
    {
    }

    #[Route('/api/category/webhook/test', name: 'api_category_webhook_test', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $event = $data['event'] ?? 'category.updated';
        $endpoint = $data['endpoint'] ?? 'http://localhost:8081/hook';
        $payload = $data['payload'] ?? ['id' => 1];
        $this->dispatcher->dispatch($event, $payload, $endpoint);

        return new JsonResponse(['status' => 'sent']);
    }
}
