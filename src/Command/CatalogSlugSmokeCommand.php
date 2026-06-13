<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Command;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\Entity\Catalog\CatalogCategorySlugHistoryEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Executes the catalog slug smoke command console workflow.
 */
#[AsCommand(name: 'category:slug:smoke')]
final class CatalogSlugSmokeCommand extends Command
{
    use CategoryCliOutputTrait;

    /**
     * Initializes the catalog slug smoke command service collaborators.
     */
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    /**
     * Runs the command workflow and returns the process status.
     */
    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $slugHistoryRepository = $this->entityManager->getRepository(CatalogCategorySlugHistoryEntity::class);
        $categoryRepository = $this->entityManager->getRepository(CatalogCategoryEntity::class);
        $slugHistories = $slugHistoryRepository->findAll();
        $foundCount = 0;
        $missingCount = 0;
        foreach ($slugHistories as $slugHistory) {
            $category = $this->findCategoryEntity($categoryRepository, $slugHistory->categoryId());
            if ($category) {
                ++$foundCount;
            } else {
                ++$missingCount;
            }
        }
        $output->writeln('Slug history ok: '.$foundCount.'; missing target: '.$missingCount);

        return 0 === $missingCount ? Command::SUCCESS : Command::FAILURE;
    }

    private function findCategoryEntity(object $repository, string $id): ?CatalogCategoryEntity
    {
        $normalizedId = trim($id);
        if ('' === $normalizedId) {
            return null;
        }

        if (is_numeric($normalizedId)) {
            $entity = $repository->find((int) $normalizedId);

            return $entity instanceof CatalogCategoryEntity ? $entity : null;
        }

        $entity = $repository->findOneBy(['slug' => $normalizedId]);
        if ($entity instanceof CatalogCategoryEntity) {
            return $entity;
        }

        $entity = $repository->find($normalizedId);

        return $entity instanceof CatalogCategoryEntity ? $entity : null;
    }
}
