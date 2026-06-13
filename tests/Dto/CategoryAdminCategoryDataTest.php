<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Tests\Dto;

use App\Cataloging\Dto\CategoryAdminCategoryData;
use PHPUnit\Framework\TestCase;

final class CategoryAdminCategoryDataTest extends TestCase
{
    public function testFromArrayNormalizesScalarValues(): void
    {
        $dto = CategoryAdminCategoryData::fromArray([
            'nameEntity' => '  Summer Collection  ',
            'slug' => '  summer-collection  ',
        ]);

        self::assertSame('Summer Collection', $dto->nameEntity);
        self::assertSame('summer-collection', $dto->slug);
    }

    public function testFromArrayFallsBackToEmptyStringsForInvalidValues(): void
    {
        $dto = CategoryAdminCategoryData::fromArray([
            'nameEntity' => ['not', 'scalar'],
            'slug' => null,
        ]);

        self::assertSame('', $dto->nameEntity);
        self::assertSame('', $dto->slug);
    }
}
