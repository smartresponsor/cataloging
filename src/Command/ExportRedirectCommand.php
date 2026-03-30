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

#[AsCommand(name: 'category:export:redirects')]
final class ExportRedirectCommand extends Command
{
    use CategoryCliOutputTrait;

    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $aliasRepo = $this->em->getRepository(CategoryAliasEntity::class);
        $catRepo = $this->em->getRepository(CategoryEntity::class);
        $aliases = $aliasRepo->findAll();

        $lines = [];
        foreach ($aliases as $alias) {
            /** @var CategoryEntity|null $cat */
            $cat = $catRepo->find($alias->categoryId());
            if (!$cat) {
                continue;
            }
            // format: oldSlug,newSlug
            $lines[] = $alias->oldSlug().','.$cat->getSlug();
        }

        $path = getcwd().'/var/export/category_redirects_301.csv';
        $this->ensureDirectory(dirname($path));
        file_put_contents($path, implode("\n", $lines));
        $output->writeln('Exported: '.$path);

        return Command::SUCCESS;
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
