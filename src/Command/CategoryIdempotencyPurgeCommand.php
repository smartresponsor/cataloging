<?php

declare(strict_types=1);

namespace App\Command;

use App\IdempotencyInterface\CategoryIdempotencyStoreInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:category:idempotency:purge')]
final class CategoryIdempotencyPurgeCommand extends Command
{
    public function __construct(private readonly CategoryIdempotencyStoreInterface $store)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $purged = $this->store->purgeExpired();
        $output->writeln(sprintf('<info>idempotency keys purged=%d</info>', $purged));

        return self::SUCCESS;
    }
}
