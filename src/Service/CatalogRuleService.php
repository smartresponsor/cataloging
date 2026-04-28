<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogRecordIndexEntity;
use App\Cataloging\Entity\Catalog\CatalogVirtualCategoryEntity;
use App\Cataloging\Message\RecomputeVirtualCategoryMessage;
use App\Cataloging\Rule\CategoryRule;
use App\Cataloging\Rule\RuleEvaluator;
use App\Cataloging\ServiceInterface\CatalogRuleServiceInterface;
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

        try {
            /** @var list<CatalogRecordIndexEntity> $records */
            $records = $this->entityManager->createQueryBuilder()
                ->select('record')
                ->from(CatalogRecordIndexEntity::class, 'record')
                ->getQuery()
                ->getResult();

            $count = 0;
            foreach ($records as $record) {
                $normalizedRecord = [
                    'brand' => $record->getBrand(),
                    'price' => $record->getPrice(),
                    'stock' => $record->getStock(),
                    'tag_set' => $record->getTagSet() ?? [],
                ];

                if ($this->ruleEvaluator->matches($normalizedRecord, $rule)) {
                    ++$count;
                }
            }

            return ['count' => $count, 'sql' => $compiled['sql']];
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Catalog rule preview requires Doctrine ORM mapping for record index.', 0, $exception);
        }
    }

    /**
     * Handles the apply workflow.
     *
     * @throws \Throwable
     */
    public function apply(string $id): bool
    {
        /** @var CatalogVirtualCategoryEntity|null $virtualCategory */
        $virtualCategory = $this->entityManager->getRepository(CatalogVirtualCategoryEntity::class)->find($id);
        if (null === $virtualCategory) {
            return false;
        }
        $this->messageBus->dispatch(new RecomputeVirtualCategoryMessage($id));

        return true;
    }
}
