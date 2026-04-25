<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CatalogRedirectRuleEntity;
use Doctrine\ORM\EntityManagerInterface;

final readonly class RedirectWriter
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /** @param list<array<string,mixed>> $rows */
    public function write(array $rows): int
    {
        $writtenCount = 0;

        $repository = $this->entityManager->getRepository(CatalogRedirectRuleEntity::class);
        foreach ($rows as $row) {
            $from = $this->stringValue($row, 'from');
            $to = $this->stringValue($row, 'to');
            $locale = $this->nullableStringValue($row, 'locale');
            if ('' === $from || '' === $to) {
                continue;
            }

            $entity = $repository->findOneBy(['fromPath' => $from, 'locale' => $locale]);
            if (!$entity instanceof CatalogRedirectRuleEntity) {
                $entity = new CatalogRedirectRuleEntity($from, $to, $locale, 'category-move');
                $this->entityManager->persist($entity);
            } else {
                $entity->changeToPath($to);
            }
            ++$writtenCount;
        }

        $this->entityManager->flush();

        return $writtenCount;
    }

    /** @param array<string,mixed> $row */
    private function stringValue(array $row, string $key): string
    {
        $value = $row[$key] ?? '';

        return is_scalar($value) ? trim((string) $value) : '';
    }

    /** @param array<string,mixed> $row */
    private function nullableStringValue(array $row, string $key): ?string
    {
        $value = $this->stringValue($row, $key);

        return '' === $value ? null : $value;
    }
}
