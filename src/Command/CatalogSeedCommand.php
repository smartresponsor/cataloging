<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Command;

use App\Cataloging\Entity\CatalogCategoryEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Executes the catalog seed command console workflow.
 */
#[AsCommand(name: 'category:seed')]
final class CatalogSeedCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;

    /**
     * Initializes the catalog seed command service collaborators.
     */
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    /**
     * Configures the command definition and available options.
     */
    protected function configure(): void
    {
        parent::configure();
        $this->addArgument('count', InputArgument::REQUIRED, 'Total categories to create (e.g., 10000 or 50000)');
        $this->addArgument('branching', InputArgument::OPTIONAL, 'Average children per node', '5');
    }

    /**
     * Runs the command workflow and returns the process status.
     */
    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $totalCount = max(1, $this->argumentInt($input, 'count', 1));
        $branchingFactor = max(2, $this->argumentInt($input, 'branching', 5));

        $root = new CatalogCategoryEntity('Root', 'root', 'root', 0);
        $this->em->persist($root);
        $list = [$root];
        $created = 1;

        $parentIndex = 0;
        while ($created < $totalCount) {
            $parent = $list[$parentIndex % count($list)];
            for ($childIndex = 0; $childIndex < $branchingFactor && $created < $totalCount; ++$childIndex) {
                $slug = $parent->getSlug().'-'.dechex($created);
                $path = $parent->getPath().'.'.$slug;
                $child = new CatalogCategoryEntity('Node '.$created, $slug, $path, $parent->getDepth() + 1);
                $this->em->persist($child);
                $list[] = $child;
                ++$created;
            }
            if (($created % 1000) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
            ++$parentIndex;
        }
        $this->em->flush();
        $output->writeln('Seeded: '.$created.' categories');

        return Command::SUCCESS;
    }
}
