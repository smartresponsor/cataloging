<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Tests\Dto;

use App\Cataloging\Dto\CategoryAdminCategoryDTO;
use PHPUnit\Framework\TestCase;

final class CategoryAdminCategoryDTOTest extends TestCase
{
    public function testFromArrayNormalizesScalarValues(): void
    {
        $dto = CategoryAdminCategoryDTO::fromArray([
            'nameEntity' => '  Summer Collection  ',
            'slug' => '  summer-collection  ',
        ]);

        self::assertSame('Summer Collection', $dto->nameEntity);
        self::assertSame('summer-collection', $dto->slug);
    }

    public function testFromArrayFallsBackToEmptyStringsForInvalidValues(): void
    {
        $dto = CategoryAdminCategoryDTO::fromArray([
            'nameEntity' => ['not', 'scalar'],
            'slug' => null,
        ]);

        self::assertSame('', $dto->nameEntity);
        self::assertSame('', $dto->slug);
    }
}
