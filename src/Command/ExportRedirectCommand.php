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
 * Executes the export redirect command console workflow.
 */
#[AsCommand(name: 'category:export:redirects')]
final class ExportRedirectCommand extends Command
{
    use CategoryCliOutputTrait;

    /**
     * Initializes the export redirect command service collaborators.
     */
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    /**
     * Runs the command workflow and returns the process status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        $slugHistoryRepo = $this->em->getRepository(CatalogCategorySlugHistoryEntity::class);
        $catRepo = $this->em->getRepository(CatalogCategoryEntity::class);
        $slugHistories = $slugHistoryRepo->findAll();

        $lines = [];
        foreach ($slugHistories as $slugHistory) {
            $cat = $this->findCategoryEntity($catRepo, $slugHistory->categoryId());
            if (!$cat) {
                continue;
            }
            // format: historicalSlug,newSlug
            $lines[] = $slugHistory->slug().','.$cat->getSlug();
        }

        $path = getcwd().'/var/export/category_redirects_301.csv';
        $this->ensureDirectory(dirname($path));
        file_put_contents($path, implode("\n", $lines));
        $output->writeln('Exported: '.$path);

        return Command::SUCCESS;
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

    private function ensureDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0o755, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Unable to create directory: %s', $path));
        }
    }
}
