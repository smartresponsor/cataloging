<?php

declare(strict_types=1);

namespace App\Cataloging\Command;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\Service\CatalogCategoryProjectionSynchronizerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Rebuilds the durable category projection directly from category write models.
 */
#[AsCommand(name: 'app:category:projection:rebuild')]
final class CategoryProjectionRebuildCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly CatalogCategoryProjectionSynchronizerService $synchronizer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();
        $this->addOption('batch-size', null, InputOption::VALUE_REQUIRED, 'Flush interval for projection synchronization.', '100');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $batchSize = max(1, (int) $input->getOption('batch-size'));
            $entityManager = $this->entityManager();
            $categories = $entityManager->getRepository(CatalogCategoryEntity::class)->findBy([], ['id' => 'ASC']);
            $processed = 0;
            $withIcon = 0;

            foreach ($categories as $category) {
                if (!$category instanceof CatalogCategoryEntity) {
                    continue;
                }

                $this->synchronizer->synchronize($category, false);
                ++$processed;
                if (null !== $category->getIconUrl() && '' !== trim($category->getIconUrl())) {
                    ++$withIcon;
                }

                if (0 === $processed % $batchSize) {
                    $this->synchronizer->flush();
                }
            }

            if (0 !== $processed % $batchSize) {
                $this->synchronizer->flush();
            }

            $output->writeln(sprintf(
                '<info>category projection rebuild processed=%d withIcon=%d batchSize=%d</info>',
                $processed,
                $withIcon,
                $batchSize,
            ));

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));

            return self::FAILURE;
        }
    }

    private function entityManager(): EntityManagerInterface
    {
        $manager = $this->registry->getManager();
        if (!$manager instanceof EntityManagerInterface) {
            throw new \RuntimeException('Doctrine entity manager is not available for category projection rebuild.');
        }

        return $manager;
    }
}
