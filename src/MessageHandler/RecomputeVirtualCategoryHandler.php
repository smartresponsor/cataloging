<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\MessageHandler;

use App\Cataloging\Entity\Catalog\CatalogVirtualCategoryEntity;
use App\Cataloging\Entity\Catalog\CatalogVirtualCategoryMemberEntity;
use App\Cataloging\Message\RecomputeVirtualCategoryMessage;
use App\Cataloging\Rule\CategoryRule;
use App\Cataloging\Rule\RuleEvaluator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handles virtual category recomputation messages.
 */
#[AsMessageHandler]
final readonly class RecomputeVirtualCategoryHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private RuleEvaluator $evaluator = new RuleEvaluator(),
    ) {
    }

    /** @throws \Throwable */
    public function __invoke(RecomputeVirtualCategoryMessage $msg): void
    {
        $vc = $this->em->getRepository(CatalogVirtualCategoryEntity::class)->find($msg->virtualCategoryId);
        if (!$vc) {
            return;
        }

        $rule = new CategoryRule($vc->getRule());

        /** @var list<\App\Cataloging\Entity\Catalog\CatalogRecordIndexEntity> $records */
        $records = $this->em->createQueryBuilder()
            ->select('record')
            ->from(\App\Cataloging\Entity\Catalog\CatalogRecordIndexEntity::class, 'record')
            ->getQuery()
            ->getResult();

        $ids = [];
        foreach ($records as $record) {
            $normalizedRecord = [
                'brand' => $record->getBrand(),
                'price' => $record->getPrice(),
                'stock' => $record->getStock(),
                'tag_set' => $record->getTagSet() ?? [],
            ];

            if ($this->evaluator->matches($normalizedRecord, $rule)) {
                $ids[] = $record->getId();
            }
        }

        $this->em->wrapInTransaction(function (EntityManagerInterface $entityManager) use ($ids, $vc): void {
            $entityManager->createQuery(
                'DELETE FROM '.CatalogVirtualCategoryMemberEntity::class.' member WHERE member.virtualCategoryId = :virtualCategoryId'
            )
                ->setParameter('virtualCategoryId', $vc->getId())
                ->execute();

            foreach ($ids as $recordId) {
                $entityManager->persist(new CatalogVirtualCategoryMemberEntity($vc->getId(), $recordId));
            }
        });
    }
}
