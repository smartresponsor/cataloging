<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 * Canon: single-hyphen names, no plurals in Class/Method, Postgres=Data, MySQL=Infrastructure
 * Tag Signin: <17111337+taa0662621456@users.noreply.github.com>
 */

namespace App\Service\Category\Domain\Rule;

final class RuleAdminService
{
    private RuleRepositoryInterface $repo;
    private RuleEngine $engine;

    public function __construct(RuleRepositoryInterface $repo, RuleEngine $engine)
    {
        $this->repo = $repo;
        $this->engine = $engine;
    }

    /**
     * Create or update a rule-based collection.
     *
     * @param array{name: string, definition: array<string, mixed>} $input
     *
     * @return string id
     */
    public function save(array $input): string
    {
        if (!isset($input['name']) || !is_string($input['name'])) {
            throw new \InvalidArgumentException('name is required');
        }
        if (!isset($input['definition']) || !is_array($input['definition'])) {
            throw new \InvalidArgumentException('definition is required');
        }

        return $this->repo->save(['name' => $input['name'], 'definition' => $input['definition']]);
    }

    /**
     * Dry-run preview for a rule against a sample payload set.
     *
     * @param list<array<string, mixed>> $payloadList
     *
     * @return array{matched: int, sample: list<array<string, mixed>>}
     */
    public function preview(string $id, array $payloadList): array
    {
        $rule = $this->repo->find($id);
        if (!$rule) {
            throw new \RuntimeException('rule not found');
        }
        $matched = [];
        foreach ($payloadList as $p) {
            if ($this->engine->match($rule['definition'], $p)) {
                $matched[] = $p;
            }
        }

        return ['matched' => count($matched), 'sample' => array_slice($matched, 0, 50)];
    }
}
