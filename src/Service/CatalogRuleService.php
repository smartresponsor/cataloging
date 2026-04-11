<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\VirtualCategoryEntity;
use App\Message\RecomputeVirtualCategoryMessage;
use App\Rule\CategoryRule;
use App\Rule\RuleEvaluator;
use App\ServiceInterface\CatalogRuleServiceInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Provides the catalog rule service application service.
 */
final readonly class CatalogRuleService implements CatalogRuleServiceInterface
{
    /**
     * Initializes the catalog rule service service collaborators.
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Connection $connection,
        private MessageBusInterface $messageBus,
        private RuleEvaluator $ruleEvaluator = new RuleEvaluator(),
    ) {
    }

    /**
     * Handles the preview workflow.
     *
     * @throws \Throwable
     */
    public function preview(array $spec): ?array
    {
        if ([] === $spec) {
            return null;
        }
        $rule = new CategoryRule($spec);
        $compiled = $this->ruleEvaluator->compile($rule);
        $sql = 'SELECT COUNT(*) AS c FROM record_index WHERE '.$compiled['sql'];
        $statement = $this->connection->prepare($sql);
        foreach ($compiled['params'] as $key => $value) {
            $statement->bindValue($key, $value);
        }
        $rawCount = $statement->executeQuery()->fetchOne();
        $count = is_numeric($rawCount) ? (int) $rawCount : 0;

        return ['count' => $count, 'sql' => $compiled['sql']];
    }

    /**
     * Handles the apply workflow.
     *
     * @throws \Throwable
     */
    public function apply(string $id): bool
    {
        /** @var VirtualCategoryEntity|null $virtualCategory */
        $virtualCategory = $this->entityManager->getRepository(VirtualCategoryEntity::class)->find($id);
        if (null === $virtualCategory) {
            return false;
        }
        $this->messageBus->dispatch(new RecomputeVirtualCategoryMessage($id));

        return true;
    }
}
