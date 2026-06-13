<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Command;

use App\Cataloging\ServiceInterface\CatalogPublicationQualityServiceInterface;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CategoryPublicationQualityEvaluationRequest;
use App\Cataloging\ValueObject\CategoryPublicationQualityInput;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Executes the category publication quality evaluate command console workflow.
 */
#[AsCommand(name: 'category:quality:evaluate')]
final class CategoryPublicationQualityEvaluateCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;

    /**
     * Initializes the category publication quality evaluate command service collaborators.
     */
    public function __construct(private readonly CatalogPublicationQualityServiceInterface $qualityService)
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
            ->setDescription('Evaluate category publication quality from CLI checks and score inputs.')
            ->setHelp(
                'Use this command to calculate publication quality, blockers, and warnings '
                .'for a category from CLI payloads.',
            )
            ->addArgument('categoryId', InputArgument::REQUIRED)
            ->addArgument('score', InputArgument::REQUIRED)
            ->addArgument('actorId', InputArgument::REQUIRED)
            ->addArgument('reason', InputArgument::REQUIRED)
            ->addOption('publication-checks', null, InputOption::VALUE_REQUIRED)
            ->addOption('checks', null, InputOption::VALUE_REQUIRED, '', '{}');
    }

    /**
     * Runs the command workflow and returns the process status.
     */
    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $publicationChecks = $this->decodeJsonMapOption($input, 'publication-checks');
            $checks = $this->decodeJsonMapOption($input, 'checks');

            $event = $this->qualityService->evaluate(
                new CategoryPublicationQualityEvaluationRequest(
                    new CategoryPublicationQualityInput(
                        $this->argumentString($input, 'categoryId'),
                        $this->argumentInt($input, 'score'),
                        $publicationChecks,
                        $checks,
                    ),
                    new CatalogAuditContext(
                        $this->argumentString($input, 'actorId'),
                        $this->argumentString($input, 'reason'),
                    ),
                ),
            );

            return $this->writeJson($output, $event->payload());
        } catch (\Throwable $exception) {
            $output->writeln((string) json_encode([
                'ok' => false,
                'error' => $exception->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::FAILURE;
        }
    }

    /**
     * @return array<string,bool>
     *
     * @throws \JsonException
     */
    private function decodeJsonMapOption(InputInterface $input, string $nameEntity): array
    {
        $decoded = json_decode($this->optionString($input, $nameEntity, '{}'), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            return [];
        }

        $result = [];
        foreach ($decoded as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            $result[$key] = (bool) $value;
        }

        return $result;
    }
}
