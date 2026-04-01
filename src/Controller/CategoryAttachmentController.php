<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\CategoryAttachmentAddRequest;
use App\Response\ApiResponseBuilder;
use App\Service\AttachmentService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryAttachmentController
{
    public function __construct(
        private readonly AttachmentService $service,
        private readonly ApiResponseBuilder $responseBuilder,
    ) {
    }

    #[Route('/api/category/attachment', name: 'api_category_attachment_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $categoryId = $request->query->get('category_id');
        $normalizedCategoryId = is_string($categoryId) ? trim($categoryId) : '';

        $payload = $this->responseBuilder->success([
            'items' => $this->service->list('' !== $normalizedCategoryId ? $normalizedCategoryId : null),
        ]);

        return new JsonResponse($payload);
    }

    #[Route('/api/category/attachment', name: 'api_category_attachment_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $input = CategoryAttachmentAddRequest::fromJson((string) $request->getContent());
        if (!$input->isValid()) {
            $payload = $this->responseBuilder->error('validation_failed', $input->getErrors(), 400);

            return new JsonResponse($payload, 400);
        }

        try {
            $item = $this->service->add($input->categoryId ?? '', $input->type, $input->path ?? '');
        } catch (\InvalidArgumentException $exception) {
            $payload = $this->responseBuilder->error('validation_failed', [$exception->getMessage()], 400);

            return new JsonResponse($payload, 400);
        }

        $payload = $this->responseBuilder->success(['item' => $item], 201);

        return new JsonResponse($payload, 201);
    }

    #[Route('/api/category/attachment/{attachmentId}', name: 'api_category_attachment_delete', methods: ['DELETE'], requirements: ['attachmentId' => '[0-9A-HJKMNP-TV-Za-hjkmnp-tv-z]{26}'])]
    public function delete(string $attachmentId): JsonResponse
    {
        try {
            $deleted = $this->service->remove($attachmentId);
        } catch (\InvalidArgumentException $exception) {
            $payload = $this->responseBuilder->error('validation_failed', [$exception->getMessage()], 400);

            return new JsonResponse($payload, 400);
        }

        if (!$deleted) {
            $payload = $this->responseBuilder->error('not_found', ['attachment was not found'], 404);

            return new JsonResponse($payload, 404);
        }

        $payload = $this->responseBuilder->success([
            'attachment_id' => trim($attachmentId),
        ]);

        return new JsonResponse($payload);
    }
}
