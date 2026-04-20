<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/** Immutable breadcrumb view for Category. */
final class CategoryBreadcrumb
{
    /** @var list<array{id:string, name:string, slug:string}> */
    private array $chain;

    /** @param list<array{id:string, name:string, slug:string}> $chain */
    public function __construct(array $chain)
    {
        $this->chain = $chain;
    }

    /** @return list<array{id:string, name:string, slug:string}> */
    public function chain(): array
    {
        return $this->chain;
    }
}
