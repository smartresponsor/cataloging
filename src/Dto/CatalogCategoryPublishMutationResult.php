<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class CatalogCategoryPublishMutationResult
{
    /** @param list<string> $blockers @param list<string> $warnings @param array<string,bool> $checks */
    public function __construct(
        private string $id,
        private bool $published,
        private string $workflowState,
        private string $previousWorkflowState,
        private array $blockers,
        private array $warnings,
        private array $checks,
        private ?string $publishedAt,
        private string $reason,
        private bool $duplicate,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function duplicate(): bool
    {
        return $this->duplicate;
    }

    /** @return array{id:string,published:bool,workflowState:string,previousWorkflowState:string,blockers:list<string>,warnings:list<string>,checks:array<string,bool>,publishedAt:?string,reason:string,duplicate:bool} */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'published' => $this->published,
            'workflowState' => $this->workflowState,
            'previousWorkflowState' => $this->previousWorkflowState,
            'blockers' => $this->blockers,
            'warnings' => $this->warnings,
            'checks' => $this->checks,
            'publishedAt' => $this->publishedAt,
            'reason' => $this->reason,
            'duplicate' => $this->duplicate,
        ];
    }
}
