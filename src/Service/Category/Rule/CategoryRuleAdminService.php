<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category\Rule;

use App\ServiceInterface\Category\CategoryRuleAdminServiceInterface;
use App\ServiceInterface\Rule\RuleRepositoryInterface;

/**
 * Provides the category rule admin service application service.
 */
final class CategoryRuleAdminService implements CategoryRuleAdminServiceInterface
{
    private const int PREVIEW_SAMPLE_LIMIT = 50;

    /**
     * Initializes the category rule admin service service collaborators.
     */
    public function __construct(
        private readonly RuleRepositoryInterface $repo,
        private readonly CategoryRuleEngine $engine,
    ) {
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
        $name = $this->requireName($input);
        $definition = $this->requireDefinition($input);

        return $this->repo->save(['name' => $name, 'definition' => $definition]);
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
        $rule = $this->requireRule($id);
        $matched = [];
        $definition = $this->requireDefinition($rule);

        foreach ($payloadList as $payload) {
            if ($this->engine->match($definition, $payload)) {
                $matched[] = $payload;
            }
        }

        return [
            'matched' => count($matched),
            'sample' => array_slice($matched, 0, self::PREVIEW_SAMPLE_LIMIT),
        ];
    }

    /**
     * @param array<string, mixed> $input
     */
    private function requireName(array $input): string
    {
        $name = $input['name'] ?? null;
        if (!is_string($name)) {
            throw new \InvalidArgumentException('name is required');
        }

        return $name;
    }

    /**
     * @param array<string, mixed> $input
     *
     * @return array<string, mixed>
     */
    private function requireDefinition(array $input): array
    {
        $definition = $input['definition'] ?? null;
        if (!is_array($definition)) {
            throw new \InvalidArgumentException('definition is required');
        }

        $normalized = [];
        foreach ($definition as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            $normalized[$key] = $value;
        }

        return $normalized;
    }

    /**
     * @return array<string, mixed>
     */
    private function requireRule(string $id): array
    {
        $rule = $this->repo->find($id);
        if (!is_array($rule)) {
            throw new \RuntimeException('rule not found');
        }

        return $rule;
    }
}
