<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Message;

use App\Entity\VirtualCategoryEntity;
use App\Rule\CategoryRule;
use App\Rule\RuleEvaluator;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RecomputeVirtualCategoryHandler
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Connection $infraConnection,
        private readonly RuleEvaluator $evaluator = new RuleEvaluator(),
    ) {
    }

    public function __invoke(RecomputeVirtualCategoryMessage $msg): void
    {
        $vc = $this->em->getRepository(VirtualCategoryEntity::class)->find($msg->virtualCategoryId);
        if (!$vc) {
            return;
        }

        $rule = new CategoryRule($vc->getRule());
        $compiled = $this->evaluator->compile($rule);

        // Select matching records from infra index
        $sql = 'SELECT id FROM record_index WHERE '.$compiled['sql'];
        $stmt = $this->infraConnection->prepare($sql);
        foreach ($compiled['params'] as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $ids = $stmt->executeQuery()->fetchFirstColumn();

        // Refresh membership
        $this->infraConnection->executeStatement(
            'DELETE FROM virtual_category_member WHERE virtual_category_id = ?',
            [$vc->getId()]
        );
        if (!empty($ids)) {
            $values = [];
            $params = [];
            foreach ($ids as $rid) {
                $values[] = '(?, ?)';
                $params[] = $vc->getId();
                $params[] = (string) $rid;
            }
            $this->infraConnection->executeStatement(
                'INSERT INTO virtual_category_member (virtual_category_id, record_id) VALUES '.implode(',', $values),
                $params
            );
        }
    }
}
