<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Request\WebhookDispatchRequest;
use App\Service\WebhookDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
/**
 * Handles the webhook controller application flow.
 */
final class WebhookController
{
    /**
     * Initializes the webhook controller service collaborators.
     */
    public function __construct(private readonly WebhookDispatcher $dispatcher)
    {
    }
    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/api/category/webhook/test', name: 'api_category_webhook_test', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
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
