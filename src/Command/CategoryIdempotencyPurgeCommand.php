<?php

declare(strict_types=1);

namespace App\Command;

use App\IdempotencyInterface\CategoryIdempotencyStoreInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Executes the category idempotency purge command console workflow.
 */
#[AsCommand(name: 'app:category:idempotency:purge')]
final class CategoryIdempotencyPurgeCommand extends Command
{
    /**
     * Initializes the category idempotency purge command service collaborators.
     */
    public function __construct(private readonly CategoryIdempotencyStoreInterface $store)
    {
        parent::__construct();
    }
    /**
     * Runs the command workflow and returns the process status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $purged = $this->store->purgeExpired();
        $output->writeln(sprintf('<info>idempotency keys purged=%d</info>', $purged));

        return self::SUCCESS;
    }
}
