<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\Request\WebhookDispatchRequest;
use App\Cataloging\Service\CatalogWebhookDispatcherService;
use App\Cataloging\ValueObject\WebhookDispatchRequest as WebhookDispatchMessageRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Handles the webhook controller application flow.
 */
final readonly class CatalogWebhookController
{
    /**
     * Initializes the webhook controller service collaborators.
     */
    public function __construct(private CatalogWebhookDispatcherService $dispatcher)
    {
    }

    /**
     * Executes the invokable workflow for this service.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \JsonException
     * @throws TransportExceptionInterface
     */
    #[Route('/api/category/webhook/test', name: 'api_category_webhook_test', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request): JsonResponse
    {
        $input = WebhookDispatchRequest::fromJson((string) $request->getContent());
        if (!$input->isValid()) {
            return new JsonResponse(['errors' => $input->getErrors()], 400);
        }

        $this->dispatcher->dispatch(new WebhookDispatchMessageRequest(
            $input->event,
            $input->endpoint,
            $input->payload,
        ));

        return new JsonResponse(['status' => 'sent']);
    }
}
