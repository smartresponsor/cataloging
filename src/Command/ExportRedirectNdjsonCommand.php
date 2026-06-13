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
 * Executes the export redirect ndjson command console workflow.
 */
#[AsCommand(name: 'category:export:redirects:ndjson')]
final class ExportRedirectNdjsonCommand extends Command
{
    use CategoryCliOutputTrait;

    /**
     * Initializes the export redirect ndjson command service collaborators.
     */
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    /**
     * Runs the command workflow and returns the process status.
     *
     * @throws \JsonException
     */
    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $slugHistoryRepo = $this->em->getRepository(CatalogCategorySlugHistoryEntity::class);
        $catRepo = $this->em->getRepository(CatalogCategoryEntity::class);
        $slugHistories = $slugHistoryRepo->findAll();

        $path = getcwd().'/var/export/category_redirects_301.ndjson';
        $this->ensureDirectory(dirname($path));
        $stream = fopen($path, 'w');
        if (false === $stream) {
            throw new \RuntimeException(sprintf('Unable to open file for writing: %s', $path));
        }

        foreach ($slugHistories as $slugHistory) {
            $category = $this->findCategoryEntity($catRepo, $slugHistory->categoryId());
            if (!$category instanceof CatalogCategoryEntity) {
                continue;
            }
            $row = [
                'from' => $slugHistory->slug(),
                'to' => $category->getSlug(),
                'ts' => $slugHistory->createdAt()->format(DATE_ATOM),
            ];
            $written = fwrite($stream, json_encode($row, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE).'
');
            if (false === $written) {
                fclose($stream);

                throw new \RuntimeException(sprintf('Unable to write export row to file: %s', $path));
            }
        }
        fclose($stream);
        $output->writeln('Exported NDJSON: '.$path);

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
