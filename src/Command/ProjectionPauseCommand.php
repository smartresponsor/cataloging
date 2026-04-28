<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Command;

use App\Cataloging\Entity\Catalog\CatalogProjectionControlEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Executes the projection pause command console workflow.
 */
#[AsCommand(name: 'category:projection:pause')]
final class ProjectionPauseCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;

    /**
     * Initializes the projection pause command service collaborators.
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
        $this->addArgument('state', InputArgument::REQUIRED, 'on|off');
    }

    /**
     * Runs the command workflow and returns the process status.
     */
    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $state = $this->argumentString($input, 'state');
        $repo = $this->em->getRepository(CatalogProjectionControlEntity::class);
        $ctrl = $repo->find('category') ?? new CatalogProjectionControlEntity();
        $ctrl->setPaused('on' === $state);
        $this->em->persist($ctrl);
        $this->em->flush();
        $output->writeln('paused='.($ctrl->paused() ? 'true' : 'false'));

        return Command::SUCCESS;
    }
}
