<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Entity\VirtualCategoryEntity;
use App\Message\RecomputeVirtualCategoryMessage;
use App\Rule\CategoryRule;
use App\Rule\RuleEvaluator;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CategoryRuleController extends AbstractController
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
        $rule = new CategoryRule($spec);
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
        /** @var VirtualCategoryEntity|null $vc */
        $vc = $this->em->getRepository(VirtualCategoryEntity::class)->find($id);
        if (!$vc) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }
        $bus->dispatch(new RecomputeVirtualCategoryMessage($id));

        return $this->json(['ok' => true]);
    }
}
