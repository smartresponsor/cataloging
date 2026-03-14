<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Controller;

use App\Service\Integration\Category\WebhookDispatcher;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class WebhookController
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly WebhookDispatcher $dispatcher,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    #[Route('/api/catalog/webhook/test', name: 'api_category_webhook_test', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $event = (string) ($data['event'] ?? 'category.updated');
        $endpoint = (string) ($data['endpoint'] ?? 'http://localhost:8081/hook');
        $payload = (array) ($data['payload'] ?? ['id' => 1]);

        try {
            $this->dispatcher->dispatch($event, $payload, $endpoint);

            return new JsonResponse([
                'status' => 'sent',
                'message' => 'The webhook test event was dispatched successfully.',
            ]);
        } catch (\Throwable $throwable) {
            $this->logger->error('Category webhook dispatch failed.', [
                'event' => $event,
                'endpoint' => $endpoint,
                'payload' => $payload,
                'exception' => $throwable,
            ]);

            return new JsonResponse([
                'status' => 'error',
                'error' => 'The webhook test event could not be dispatched.',
                'message' => 'Please verify the endpoint and check the logs.',
            ], 500);
        }
    }
}
