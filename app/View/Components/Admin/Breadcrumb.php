<?php

declare(strict_types=1);

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

final class Breadcrumb extends Component
{
    public readonly array $items;

    public function __construct(array $items = [])
    {
        $this->items = $this->processItems($items);
    }

    public function render(): View
    {
        return view('components.admin.breadcrumb');
    }

    private function processItems(array $items): array
    {
        $processed = [];

        foreach ($items as $item) {
            if (is_string($item)) {
                // Simple string = current page (no link)
                $processed[] = [
                    'label'   => $item,
                    'href'    => null,
                    'current' => true,
                ];
            } elseif (is_array($item)) {
                // Array with label and href
                $processed[] = [
                    'label'   => $item['label'] ?? $item[0] ?? '',
                    'href'    => $item['href']  ?? $item['url'] ?? $item[1] ?? null,
                    'current' => false,
                ];
            }
        }

        return $processed;
    }
}
