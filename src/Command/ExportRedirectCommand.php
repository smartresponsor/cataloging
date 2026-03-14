<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Command;

use App\Entity\CategoryAliasEntity;
use App\Entity\CategoryEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:export:redirects')]
final class ExportRedirectCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $aliasRepo = $this->em->getRepository(CategoryAliasEntity::class);
            $catRepo = $this->em->getRepository(CategoryEntity::class);
            $aliases = $aliasRepo->findAll();

            $lines = [];
            foreach ($aliases as $alias) {
                /** @var CategoryEntity|null $category */
                $category = $catRepo->find($alias->categoryId());
                if (null === $category) {
                    continue;
                }

                $lines[] = $alias->oldSlug().','.$category->getSlug();
            }

            $path = getcwd().'/var/export/category_redirects_301.csv';
            $dir = dirname($path);
            if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new \RuntimeException('The export directory could not be created.');
            }

            if (false === file_put_contents($path, implode("\n", $lines))) {
                throw new \RuntimeException('The redirect export file could not be written.');
            }

            $output->writeln('Redirect export completed successfully: '.$path);

            return Command::SUCCESS;
        } catch (\Throwable $throwable) {
            $output->writeln('<error>The redirect export failed. Check the logs or runtime output for details.</error>');
            $output->writeln('<comment>'.$throwable->getMessage().'</comment>');

            return Command::FAILURE;
        }
    }
}
