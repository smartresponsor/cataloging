<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\Entity\CategoryAliasEntity;
use App\Entity\CategoryEntity;
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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $aliasRepository = $this->entityManager->getRepository(CategoryAliasEntity::class);
        $categoryRepository = $this->entityManager->getRepository(CategoryEntity::class);
        $aliases = $aliasRepository->findAll();
        $foundCount = 0;
        $missingCount = 0;
        foreach ($aliases as $alias) {
            $category = $categoryRepository->find($alias->categoryId());
            if ($category) {
                ++$foundCount;
            } else {
                ++$missingCount;
            }
        }
        $output->writeln('Alias ok: '.$foundCount.'; missing target: '.$missingCount);

        return 0 === $missingCount ? Command::SUCCESS : Command::FAILURE;
    }
}
