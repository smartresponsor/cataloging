<?php

declare(strict_types=1);

namespace App\Service;

final class RuleValidator
{
    /** @var list<string> */
    private array $allowedKeys = ['id', 'name', 'slug', 'path', 'depth', 'color', 'tags'];

    /**
     * @param array<mixed> $rules
     *
     * @return list<string>
     */
    public function validate(array $rules): array
    {
        if ([] === $rules) {
            return ['rules must not be empty'];
        }

        $errors = [];
        foreach ($rules as $key => $value) {
            if (!is_string($key) || '' === trim($key)) {
                $errors[] = 'rule keys must be non-empty strings';
                continue;
            }

            if (!in_array($key, $this->allowedKeys, true)) {
                $errors[] = sprintf('unsupported rule key: %s', $key);
                continue;
            }

            if (is_bool($value) || is_float($value) || is_int($value) || is_string($value)) {
                continue;
            }

            if (!is_array($value)) {
                $errors[] = sprintf('rule value for %s must be scalar or list', $key);
                continue;
            }

            foreach ($value as $item) {
                if (!is_bool($item) && !is_float($item) && !is_int($item) && !is_string($item)) {
                    $errors[] = sprintf('rule list for %s must contain only scalar values', $key);
                    break;
                }
            }
        }

        return array_values(array_unique($errors));
    }
}
