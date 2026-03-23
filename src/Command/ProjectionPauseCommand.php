<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\Entity\ProjectionControlEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:projection:pause')]
final class ProjectionPauseCommand extends Command
{
    use CategoryCliOutputTrait;

    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('state', InputArgument::REQUIRED, 'on|off');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $state = (string) $input->getArgument('state');
        $repo = $this->em->getRepository(ProjectionControlEntity::class);
        $ctrl = $repo->find('category') ?? new ProjectionControlEntity();
        $ctrl->setPaused('on' === $state);
        $this->em->persist($ctrl);
        $this->em->flush();
        $output->writeln('paused='.($ctrl->paused() ? 'true' : 'false'));

        return Command::SUCCESS;
    }
}
