<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Controller;

use App\Entity\VirtualCategoryEntity;
use App\Message\RecomputeVirtualCategoryMessage;
use App\Rule\CategoryRule;
use App\Rule\RuleEvaluator;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CategoryRuleController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Connection $connection,
        private readonly RuleEvaluator $evaluator = new RuleEvaluator(),
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    #[Route('/api/catalog/virtual/preview', name: 'api_category_virtual_preview', methods: ['POST'])]
    #[IsGranted('category.rule')]
    public function preview(Request $request): JsonResponse
    {
        $spec = json_decode((string) $request->getContent(), true);
        if (!is_array($spec)) {
            return $this->json([
                'ok' => false,
                'error' => 'The rule preview payload must be valid JSON.',
            ], 400);
        }

        try {
            $rule = new CategoryRule($spec);
            $compiled = $this->evaluator->compile($rule);

            $statement = $this->connection->prepare(
                'SELECT COUNT(*) AS c FROM record_index WHERE '.$compiled['sql']
            );

            foreach ($compiled['params'] as $key => $value) {
                $statement->bindValue($key, $value);
            }

            $count = (int) $statement->executeQuery()->fetchOne();
            $limit = (int) ($_ENV['RULE_MAX_CARDINALITY'] ?? 100000);

            if ($count > $limit) {
                return $this->json([
                    'ok' => false,
                    'error' => 'The rule result exceeds the allowed cardinality limit.',
                    'limit' => $limit,
                    'count' => $count,
                ], 413);
            }

            return $this->json([
                'ok' => true,
                'item' => [
                    'count' => $count,
                    'sql' => $compiled['sql'],
                ],
                'message' => 'The virtual category rule preview was generated successfully.',
            ]);
        } catch (\Throwable $throwable) {
            $this->logger->error('Virtual category rule preview failed.', [
                'spec' => $spec,
                'exception' => $throwable,
            ]);

            return $this->json([
                'ok' => false,
                'error' => 'The rule preview could not be generated.',
                'message' => 'Please review the rule definition and try again. Check the logs if the problem continues.',
            ], 500);
        }
    }

    #[Route('/api/catalog/virtual/apply/{id}', name: 'api_category_virtual_apply', methods: ['POST'])]
    #[IsGranted('category.rule')]
    public function apply(string $id, MessageBusInterface $messageBus): JsonResponse
    {
        try {
            /** @var VirtualCategoryEntity|null $virtualCategory */
            $virtualCategory = $this->entityManager->getRepository(VirtualCategoryEntity::class)->find($id);

            if (null === $virtualCategory) {
                return $this->json([
                    'ok' => false,
                    'error' => 'The virtual category could not be found.',
                ], 404);
            }

            $messageBus->dispatch(new RecomputeVirtualCategoryMessage($id));

            return $this->json([
                'ok' => true,
                'message' => 'The virtual category recompute job was queued successfully.',
            ]);
        } catch (\Throwable $throwable) {
            $this->logger->error('Virtual category apply failed.', [
                'id' => $id,
                'exception' => $throwable,
            ]);

            return $this->json([
                'ok' => false,
                'error' => 'The virtual category could not be queued for recompute.',
                'message' => 'Please try again. Check the logs if the problem continues.',
            ], 500);
        }
    }
}
