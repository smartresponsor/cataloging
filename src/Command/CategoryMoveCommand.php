<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\CategoryMoveInterface;
use App\ValueObject\CatalogMoveRequest;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Executes the category move command console workflow.
 */
#[AsCommand(name: 'category:move')]
final class CategoryMoveCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;

    /**
     * Initializes the category move command service collaborators.
     */
    public function __construct(private readonly CategoryMoveInterface $moveService)
    {
        parent::__construct();
    }

    /**
     * Configures the command definition and available options.
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setDescription('Move a category node under a new parent and print operation result.')
            ->setHelp(
                'Use this command to run a category move operation with optional dry-run mode '
                .'and print the result as JSON.',
            )
            ->addArgument('nodeId', InputArgument::REQUIRED)
            ->addArgument('newParentId', InputArgument::REQUIRED)
            ->addArgument('treeId', InputArgument::REQUIRED)
            ->addArgument('policy', InputArgument::REQUIRED)
            ->addOption('dry-run', null, InputOption::VALUE_NONE)
            ->addOption('locale', null, InputOption::VALUE_REQUIRED);
    }

    /**
     * Runs the command workflow and returns the process status.
     *
     * @throws \JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->moveService->move(new CatalogMoveRequest(
            $this->argumentString($input, 'nodeId'),
            $this->argumentString($input, 'newParentId'),
            $this->argumentString($input, 'treeId'),
            $this->argumentString($input, 'policy'),
            (bool) $input->getOption('dry-run'),
            $this->nonEmptyString($this->optionString($input, 'locale')) ?: null,
        ));
        [$changed, $redirects] = $result;

        return $this->writeJson($output, [
            'changed' => $changed,
            'redirects' => $redirects,
        ]);
    }
}
