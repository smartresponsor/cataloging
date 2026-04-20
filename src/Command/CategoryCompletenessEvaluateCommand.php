<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Command;

use App\Cataloging\ServiceInterface\CatalogCompletenessServiceInterface;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CategoryEvaluationRequest;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Executes the category completeness evaluate command console workflow.
 */
#[AsCommand(name: 'category:completeness:evaluate')]
final class CategoryCompletenessEvaluateCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;

    /**
     * Initializes the category completeness evaluate command service collaborators.
     */
    public function __construct(private readonly CatalogCompletenessServiceInterface $completenessService)
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
            ->setDescription('Evaluate category completeness from a CLI payload.')
            ->setHelp(
                'Use this command to pass completeness input payloads from the CLI '
                .'and print the resulting report as JSON.',
            )
            ->addArgument('categoryId', InputArgument::REQUIRED)
            ->addArgument('actorId', InputArgument::REQUIRED)
            ->addArgument('reason', InputArgument::REQUIRED)
            ->addOption('payload', null, InputOption::VALUE_REQUIRED);
    }

    /**
     * Runs the command workflow and returns the process status.
     *
     * @throws \JsonException
     */
    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $payload = $this->jsonOptionMap($input, 'payload');

        $event = $this->completenessService->evaluate(
            new CategoryEvaluationRequest(
                $this->argumentString($input, 'categoryId'),
                $payload,
                new CatalogAuditContext(
                    $this->argumentString($input, 'actorId'),
                    $this->argumentString($input, 'reason'),
                ),
            ),
        );

        return $this->writeJson($output, $event->payload());
    }
}
