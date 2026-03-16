<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Controller;

use App\Entity\VirtualtestsEntity;
use App\Message\RecomputeVirtualtestsMessage;
use App\Rule\RuleEvaluator;
use App\Rule\testsRule;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class testsRuleController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Connection $infra,
        private readonly RuleEvaluator $eval = new RuleEvaluator(),
    ) {
    }

    #[Route('/api/category/virtual/preview', name: 'api_category_virtual_preview', methods: ['POST'])]
    #[IsGranted('category.rule')]
    public function preview(Request $r): JsonResponse
    {
        $spec = json_decode((string) $r->getContent(), true);
        if (!is_array($spec)) {
            return $this->json(['ok' => false, 'error' => 'bad_spec'], 400);
        }
        $rule = new testsRule($spec);
        $compiled = $this->eval->compile($rule);
        $sql = 'SELECT COUNT(*) AS c FROM record_index WHERE '.$compiled['sql'];
        $stmt = $this->infra->prepare($sql);
        foreach ($compiled['params'] as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $count = (int) $stmt->executeQuery()->fetchOne();
        $limit = (int) ($_ENV['RULE_MAX_CARDINALITY'] ?? 100000);
        if ($count > $limit) {
            return $this->json(['ok' => false, 'error' => 'cardinality_exceeds', 'limit' => $limit, 'count' => $count], 413);
        }

        return $this->json(['ok' => true, 'item' => ['count' => $count, 'sql' => $compiled['sql']]]);
    }

    #[Route('/api/category/virtual/apply/{id}', name: 'api_category_virtual_apply', methods: ['POST'])]
    #[IsGranted('category.rule')]
    public function apply(string $id, MessageBusInterface $bus): JsonResponse
    {
        /** @var VirtualtestsEntity|null $vc */
        $vc = $this->em->getRepository(VirtualtestsEntity::class)->find($id);
        if (!$vc) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }
        $bus->dispatch(new RecomputeVirtualtestsMessage($id));

        return $this->json(['ok' => true]);
    }
}
