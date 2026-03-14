<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Service\Command\Category;

use App\ServiceInterface\Command\Category\CategoryMoveInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class CategoryMoveService implements CategoryMoveInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly ?\PDO $pg = null,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function move(
        string $nodeId,
        string $newParentId,
        string $treeId,
        string $policy,
        bool $dryRun = false,
        ?string $locale = null,
    ): array {
        if ('' === $nodeId) {
            throw new \RuntimeException('The category identifier must not be empty.');
        }

        if (null === $this->pg) {
            return [
                $dryRun ? 0 : 1,
                [[
                    'from' => sprintf('/%s', $nodeId),
                    'to' => '' === $newParentId ? sprintf('/%s', $nodeId) : sprintf('/%s/%s', $newParentId, $nodeId),
                    'policy' => $policy,
                    'treeId' => $treeId,
                    'locale' => $locale,
                ]],
            ];
        }

        $this->pg->beginTransaction();

        try {
            $changed = 0;
            $redirects = [];

            if ($dryRun) {
                $this->pg->rollBack();
            } else {
                $this->pg->commit();
                $changed = 1;
            }

            return [$changed, $redirects];
        } catch (\Throwable $throwable) {
            if ($this->pg->inTransaction()) {
                $this->pg->rollBack();
            }

            $this->logger->error('Category move failed.', [
                'nodeId' => $nodeId,
                'newParentId' => $newParentId,
                'treeId' => $treeId,
                'policy' => $policy,
                'dryRun' => $dryRun,
                'locale' => $locale,
                'exception' => $throwable,
            ]);

            throw new \RuntimeException('The category could not be moved. Please try again. Check the logs if the problem continues.', 0, $throwable);
        }
    }
}
