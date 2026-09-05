<?php

declare(strict_types=1);

namespace App\Cataloging\Value\Surface;

use App\Interfacing\Contract\InterfaceSurfaceRenderableInterface;

final readonly class CatalogContract implements InterfaceSurfaceRenderableInterface
{
    public const WORD = 'catalog';
    public const VIEW_BASE = 'index';

    /**
     * @param array<string, string> $slotMap
     * @param array<string, mixed>  $slots
     */
    public function __construct(
        public string $word,
        public string $view,
        public string $templateName,
        public array $slotMap,
        public string $catalogToken,
        public array $slots,
    ) {
    }

    /**
     * @return array{word: string, view: string, templateName: string, slotMap: array<string, string>, catalogToken: string, slots: array<string, mixed>}
     */
    public function toTemplateContext(): array
    {
        return [
            'word' => $this->word,
            'view' => $this->view,
            'templateName' => $this->templateName,
            'slotMap' => $this->slotMap,
            'catalogToken' => $this->catalogToken,
            'slots' => $this->slots,
        ];
    }

    /**
     * @return array{word: string, view: string, catalogToken: string, slots: array<string, mixed>}
     */
    public function toFallbackData(): array
    {
        return [
            'word' => $this->word,
            'view' => $this->view,
            'catalogToken' => $this->catalogToken,
            'slots' => $this->slots,
        ];
    }

    public function templateName(): string
    {
        return $this->templateName;
    }
}
