<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the seo per locale application service.
 */
final class SeoPerLocale
{
    /**
     * @param array{id?:mixed,locale?:mixed,name?:mixed,slug?:mixed} $category
     *
     * @return array{id:?string,locale:string,title:string,description:string,canonical:string}
     */
    public function build(array $category): array
    {
        $locale = $this->stringValue($category, 'locale', 'en');
        $name = $this->stringValue($category, 'name');
        $slug = $this->stringValue($category, 'slug');
        $title = 'uk' === $locale ? 'Категорія: '.$name : 'Category: '.$name;
        $canonical = 'https://example.com/category/'.$slug.'?lang='.$locale;
        $id = $this->stringValue($category, 'id');
        $data = [
            'id' => '' !== $id ? $id : null,
            'locale' => $locale,
            'title' => $title,
            'description' => $title.' description',
            'canonical' => $canonical,
        ];
        file_put_contents('report/category-seo-locale.json', json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return $data;
    }

    /** @param array<string,mixed> $category */
    private function stringValue(array $category, string $key, string $default = ''): string
    {
        $value = $category[$key] ?? $default;

        return is_scalar($value) ? (string) $value : $default;
    }
}
