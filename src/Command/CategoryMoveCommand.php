<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\CategoryMoveInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:move')]
final class CategoryMoveCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;

    public function __construct(private readonly CategoryMoveInterface $moveService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Move a category node under a new parent and print operation result.')
            ->setHelp('Use this command to run a category move operation with optional dry-run mode and print the result as JSON.')
            ->addArgument('nodeId', InputArgument::REQUIRED)
            ->addArgument('newParentId', InputArgument::REQUIRED)
            ->addArgument('treeId', InputArgument::REQUIRED)
            ->addArgument('policy', InputArgument::REQUIRED)
            ->addOption('dry-run', null, InputOption::VALUE_NONE)
            ->addOption('locale', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->moveService->move(
            $this->argumentString($input, 'nodeId'),
            $this->argumentString($input, 'newParentId'),
            $this->argumentString($input, 'treeId'),
            $this->argumentString($input, 'policy'),
            (bool) $input->getOption('dry-run'),
            $this->nonEmptyString($this->optionString($input, 'locale'), '') ?: null,
        );

        return $this->writeJson($output, [
            'changed' => $result[0] ?? 0,
            'redirects' => is_array($result[1] ?? null) ? $result[1] : [],
        ]);
    }
}
