<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Message;

use App\Cataloging\Entity\VirtualCategoryEntity;
use App\Cataloging\Rule\CategoryRule;
use App\Cataloging\Rule\RuleEvaluator;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Provides the recompute virtual category handler implementation.
 */
#[AsMessageHandler]
final readonly class RecomputeVirtualCategoryHandler
{
    /**
     * Initializes the recompute virtual category handler service collaborators.
     */
    public function __construct(
        private EntityManagerInterface $em,
        private Connection $infraConnection,
        private RuleEvaluator $evaluator = new RuleEvaluator(),
    ) {
    }

    /**
     * Executes the invokable workflow for this service.
     *
     * @throws \Throwable
     */
    public function __invoke(RecomputeVirtualCategoryMessage $msg): void
    {
        $vc = $this->em->getRepository(VirtualCategoryEntity::class)->find($msg->virtualCategoryId);
        if (!$vc) {
            return;
        }

        $rule = new CategoryRule($vc->getRule());
        $compiled = $this->evaluator->compile($rule);

        $sql = 'SELECT id FROM record_index WHERE '.$compiled['sql'];
        $stmt = $this->infraConnection->prepare($sql);
        foreach ($compiled['params'] as $parameterKey => $parameterValue) {
            $stmt->bindValue($parameterKey, $parameterValue);
        }
        $ids = $stmt->executeQuery()->fetchFirstColumn();

        $this->infraConnection->executeStatement(
            'DELETE FROM virtual_category_member WHERE virtual_category_id = ?',
            [$vc->getId()],
        );
        if ([] === $ids) {
            return;
        }

        $values = [];
        $params = [];
        foreach ($ids as $recordId) {
            if (!is_scalar($recordId) && null !== $recordId) {
                continue;
            }
            $values[] = '(?, ?)';
            $params[] = $vc->getId();
            $params[] = (string) $recordId;
        }

        if ([] === $values) {
            return;
        }

        $this->infraConnection->executeStatement(
            'INSERT INTO virtual_category_member (virtual_category_id, record_id) VALUES '.implode(',', $values),
            $params,
        );
    }
}
