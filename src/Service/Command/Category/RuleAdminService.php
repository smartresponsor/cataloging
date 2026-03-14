<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Service\Command\Category;

use App\ServiceInterface\Command\Category\RuleRepositoryInterface;

class RuleAdminService
{
    public function __construct(
        private readonly RuleRepositoryInterface $repo,
        private readonly RuleEngine $engine,
    ) {
    }

    public function save(array $input): string
    {
        if (!isset($input['name']) || !is_string($input['name']) || '' === $input['name']) {
            throw new \InvalidArgumentException('Rule name is required.');
        }
        if (!isset($input['definition']) || !is_array($input['definition'])) {
            throw new \InvalidArgumentException('Rule definition is required.');
        }

        return $this->repo->save(['name' => $input['name'], 'definition' => $input['definition']]);
    }

    public function preview(string $id, array $payloadList): array
    {
        $rule = $this->repo->find($id);
        if (null === $rule) {
            throw new \RuntimeException('Rule not found.');
        }

        $matched = [];
        foreach ($payloadList as $payload) {
            if (is_array($payload) && $this->engine->match($rule['definition'], $payload)) {
                $matched[] = $payload;
            }
        }

        return ['matched' => count($matched), 'sample' => array_slice($matched, 0, 50)];
    }
}
