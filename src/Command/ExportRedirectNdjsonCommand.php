<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */
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

        $path = getcwd().'/var/export/category_redirects_301.ndjson';
        $this->ensureDirectory(dirname($path));
        $fh = fopen($path, 'w');

        foreach ($aliases as $alias) {
            $cat = $catRepo->find($alias->categoryId());
            if (!$cat) {
                continue;
            }
            $row = [
                'from' => (string) $alias->oldSlug(),
                'to' => (string) $cat->getSlug(),
                'ts' => $alias->createdAt()->format(DATE_ATOM),
            ];
            fwrite($fh, json_encode($row, JSON_UNESCAPED_UNICODE)."\n");
        }
        fclose($fh);
        $output->writeln('Exported NDJSON: '.$path);

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
