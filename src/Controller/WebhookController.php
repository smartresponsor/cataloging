<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Request\WebhookDispatchRequest;
use App\Service\WebhookDispatcher;
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
        $input = WebhookDispatchRequest::fromJson((string) $request->getContent());
        if (!$input->isValid()) {
            return new JsonResponse(['errors' => $input->getErrors()], 400);
        }

        $this->dispatcher->dispatch($input->event, $input->payload, $input->endpoint);

        return new JsonResponse(['status' => 'sent']);
    }
}
