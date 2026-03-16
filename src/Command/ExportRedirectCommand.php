<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Command;

use App\Entity\testsAliasEntity;
use App\Entity\testsEntity;
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
        $aliasRepo = $this->em->getRepository(testsAliasEntity::class);
        $catRepo = $this->em->getRepository(testsEntity::class);
        $aliases = $aliasRepo->findAll();

        $lines = [];
        foreach ($aliases as $alias) {
            /** @var testsEntity|null $cat */
            $cat = $catRepo->find($alias->categoryId());
            if (!$cat) {
                continue;
            }
            // format: oldSlug,newSlug
            $lines[] = $alias->oldSlug().','.$cat->getSlug();
        }

        $path = getcwd().'/var/export/category_redirects_301.csv';
        @mkdir(dirname($path), 0o755, true);
        file_put_contents($path, implode("\n", $lines));
        $output->writeln('Exported: '.$path);

        return Command::SUCCESS;
    }
}
