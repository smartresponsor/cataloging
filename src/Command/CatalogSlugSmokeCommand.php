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
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }
    /**
     * Runs the command workflow and returns the process status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $aliasRepo = $this->em->getRepository(CategoryAliasEntity::class);
        $catRepo = $this->em->getRepository(CategoryEntity::class);
        $aliases = $aliasRepo->findAll();
        $ok = 0;
        $miss = 0;
        foreach ($aliases as $a) {
            $cat = $catRepo->find($a->categoryId());
            if ($cat) {
                ++$ok;
            } else {
                ++$miss;
            }
        }
        $output->writeln('Alias ok: '.$ok.'; missing target: '.$miss);

        return 0 === $miss ? Command::SUCCESS : Command::FAILURE;
    }
}
