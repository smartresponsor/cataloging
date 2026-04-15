<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class CatalogCategoryMoveMutationResult
{
    /** @param list<array{id:string,from:string,to:string}> $redirects */
    public function __construct(
        private string $id,
        private ?string $oldParentId,
        private string $newParentId,
        private string $treeId,
        private string $policy,
        private int $changedCount,
        private bool $dryRun,
        private array $redirects,
        private bool $duplicate,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function dryRun(): bool
    {
        return $this->dryRun;
    }

    public function duplicate(): bool
    {
        return $this->duplicate;
    }

    /** @return array{id:string,oldParentId:?string,newParentId:string,treeId:string,policy:string,changedCount:int,dryRun:bool,redirects:list<array{id:string,from:string,to:string}>,duplicate:bool} */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'oldParentId' => $this->oldParentId,
            'newParentId' => $this->newParentId,
            'treeId' => $this->treeId,
            'policy' => $this->policy,
            'changedCount' => $this->changedCount,
            'dryRun' => $this->dryRun,
            'redirects' => $this->redirects,
            'duplicate' => $this->duplicate,
        ];
    }
}
