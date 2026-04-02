<?php

declare(strict_types=1);

namespace App\Tests\Contract\Http;

use Symfony\Component\Yaml\Yaml;

trait OpenApiResponseSchemaAssertionTrait
{
    /** @var array<string, mixed>|null */
    private static ?array $runtimeOpenApiSpec = null;

    /**
     * @param array<string, mixed> $payload
     */
    private function assertPayloadMatchesSchema(array $payload, string $schemaName): void
    {
        $spec = self::getRuntimeOpenApiSpec();
        $schemas = $spec['components']['schemas'] ?? [];

        self::assertIsArray($schemas);
        self::assertArrayHasKey($schemaName, $schemas, sprintf('Missing schema "%s".', $schemaName));

        $schema = $schemas[$schemaName];
        self::assertIsArray($schema);

        $this->assertValueMatchesSchema($payload, $schema, '#/components/schemas/' . $schemaName);
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $schema
     */
    private function assertValueMatchesSchema(mixed $value, array $schema, string $path): void
    {
        if (isset($schema['$ref']) && is_string($schema['$ref'])) {
            $resolvedSchema = $this->resolveSchemaReference($schema['$ref']);
            $this->assertValueMatchesSchema($value, $resolvedSchema, $schema['$ref']);

            return;
        }

        if (isset($schema['allOf']) && is_array($schema['allOf'])) {
            foreach ($schema['allOf'] as $index => $part) {
                self::assertIsArray($part);
                $this->assertValueMatchesSchema($value, $part, $path . '/allOf/' . $index);
            }

            return;
        }

        $type = $schema['type'] ?? null;
        if (is_string($type)) {
            $this->assertTypeMatches($value, $type, $path);
        }

        if (isset($schema['enum']) && is_array($schema['enum'])) {
            self::assertContains($value, $schema['enum'], sprintf('Value at %s is not in enum.', $path));
        }

        if ('object' === $type) {
            self::assertIsArray($value, sprintf('Value at %s must be an object array.', $path));

            foreach (($schema['required'] ?? []) as $requiredKey) {
                self::assertIsString($requiredKey);
                self::assertArrayHasKey($requiredKey, $value, sprintf('Missing required key "%s" at %s.', $requiredKey, $path));
            }

            $properties = $schema['properties'] ?? [];
            if (is_array($properties)) {
                foreach ($properties as $propertyName => $propertySchema) {
                    if (!array_key_exists($propertyName, $value)) {
                        continue;
                    }

                    self::assertIsArray($propertySchema);
                    $this->assertValueMatchesSchema($value[$propertyName], $propertySchema, $path . '/properties/' . $propertyName);
                }
            }

            return;
        }

        if ('array' === $type) {
            self::assertIsArray($value, sprintf('Value at %s must be an array.', $path));
            if (isset($schema['items']) && is_array($schema['items'])) {
                foreach ($value as $index => $item) {
                    $this->assertValueMatchesSchema($item, $schema['items'], $path . '/items/' . (string) $index);
                }
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveSchemaReference(string $ref): array
    {
        self::assertStringStartsWith('#/components/schemas/', $ref, sprintf('Unsupported schema reference "%s".', $ref));

        $schemaName = substr($ref, strlen('#/components/schemas/'));
        $schemas = self::getRuntimeOpenApiSpec()['components']['schemas'] ?? [];

        self::assertIsArray($schemas);
        self::assertArrayHasKey($schemaName, $schemas, sprintf('Referenced schema "%s" is missing.', $schemaName));
        self::assertIsArray($schemas[$schemaName]);

        return $schemas[$schemaName];
    }

    private function assertTypeMatches(mixed $value, string $type, string $path): void
    {
        match ($type) {
            'object' => self::assertIsArray($value, sprintf('Value at %s must be an object array.', $path)),
            'array' => self::assertIsArray($value, sprintf('Value at %s must be an array.', $path)),
            'string' => self::assertIsString($value, sprintf('Value at %s must be a string.', $path)),
            'integer' => self::assertIsInt($value, sprintf('Value at %s must be an integer.', $path)),
            'boolean' => self::assertIsBool($value, sprintf('Value at %s must be a boolean.', $path)),
            default => self::fail(sprintf('Unsupported schema type "%s" at %s.', $type, $path)),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private static function getRuntimeOpenApiSpec(): array
    {
        if (null === self::$runtimeOpenApiSpec) {
            $spec = Yaml::parseFile(dirname(__DIR__, 3) . '/api/catalog-openapi.runtime.yaml');
            self::assertIsArray($spec);
            self::$runtimeOpenApiSpec = $spec;
        }

        return self::$runtimeOpenApiSpec;
    }
}
