<?php

declare(strict_types=1);

namespace App\Command;

use App\RunnerInterface\CategoryProjectionRunnerInterface;
use App\Service\ProjectionWorker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Executes the category projection run command console workflow.
 */
#[AsCommand(name: 'app:category:projection:run')]
final class CategoryProjectionRunCommand extends Command
{
    /**
     * Initializes the category projection run command service collaborators.
     */
    public function __construct(
        private readonly ProjectionWorker $worker,
        private readonly ?CategoryProjectionRunnerInterface $runner = null,
    ) {
        parent::__construct();
    }
    /**
     * Configures the command definition and available options.
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('once', null, InputOption::VALUE_NONE, 'Process a single projection batch.')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Maximum messages for a single batch.', '50')
            ->addOption('max-sec', null, InputOption::VALUE_REQUIRED, 'Maximum seconds for loop mode.', '5')
            ->addOption('max-batch', null, InputOption::VALUE_REQUIRED, 'Maximum messages for loop mode.', '100');
    }
    /**
     * Runs the command workflow and returns the process status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        $limit = max(1, (int) $input->getOption('limit'));
        if ((bool) $input->getOption('once')) {
            $processed = $this->worker->runOnce($limit);
            $output->writeln(sprintf('<info>projection batch processed=%d</info>', $processed));

            return self::SUCCESS;
        }

        $maxSec = max(1, (int) $input->getOption('max-sec'));
        $maxBatch = max(1, (int) $input->getOption('max-batch'));
        if (null === $this->runner) {
            $processed = $this->worker->runOnce(min($limit, $maxBatch));
            $output->writeln(sprintf('<comment>runner not wired, fallback processed=%d</comment>', $processed));

            return self::SUCCESS;
        }

        $this->runner->run($maxSec, $maxBatch);
        $output->writeln(sprintf('<info>projection runner completed maxSec=%d maxBatch=%d</info>', $maxSec, $maxBatch));

        return self::SUCCESS;
    }
}
