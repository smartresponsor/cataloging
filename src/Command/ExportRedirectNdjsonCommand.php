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

#[AsCommand(name: 'category:export:redirects:ndjson')]
final class ExportRedirectNdjsonCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = getcwd().'/var/export/category_redirects_301.ndjson';

        try {
            $aliasRepo = $this->em->getRepository(CategoryAliasEntity::class);
            $catRepo = $this->em->getRepository(CategoryEntity::class);
            $aliases = $aliasRepo->findAll();

            $dir = dirname($path);
            if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new \RuntimeException('The export directory could not be created.');
            }

            $handle = fopen($path, 'w');
            if (false === $handle) {
                throw new \RuntimeException('The NDJSON export file could not be opened for writing.');
            }

            try {
                foreach ($aliases as $alias) {
                    $category = $catRepo->find($alias->categoryId());
                    if (null === $category) {
                        continue;
                    }

                    $row = [
                        'from' => (string) $alias->oldSlug(),
                        'to' => (string) $category->getSlug(),
                        'ts' => $alias->createdAt()->format(DATE_ATOM),
                    ];

                    $payload = json_encode($row, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)."\n";
                    if (false === fwrite($handle, $payload)) {
                        throw new \RuntimeException('The NDJSON export row could not be written.');
                    }
                }
            } finally {
                fclose($handle);
            }

            $output->writeln('NDJSON redirect export completed successfully: '.$path);

            return Command::SUCCESS;
        } catch (\JsonException|\Throwable $throwable) {
            $output->writeln('<error>The NDJSON redirect export failed. Check the logs or runtime output for details.</error>');
            $output->writeln('<comment>'.$throwable->getMessage().'</comment>');

            return Command::FAILURE;
        }
    }
}
