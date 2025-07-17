<?php

declare(strict_types=1);

namespace App\View\Components\Ui;

use Illuminate\View\Component;
use Illuminate\View\View;

final class Toast extends Component
{
    public readonly string $position;

    public readonly int $duration;

    public function __construct()
    {
        $config         = $this->getToastConfig();
        $this->position = $this->getPositionClasses($config['position']);
        $this->duration = $config['duration'];
    }

    public function render(): View
    {
        return view('components.ui.toast');
    }

    private function getToastConfig(): array
    {
        // TODO: Plus tard, récupérer depuis config('ui.toast') ou préférences utilisateur
        return [
            'position' => 'top-end',
            'duration' => 3000,
        ];
    }

    private function getPositionClasses(string $position): string
    {
        return match ($position) {
            'top-start'     => 'toast-top toast-start',
            'top-center'    => 'toast-top toast-center',
            'top-end'       => 'toast-top toast-end',
            'middle-start'  => 'toast-middle toast-start',
            'middle-center' => 'toast-middle toast-center',
            'middle-end'    => 'toast-middle toast-end',
            'bottom-start'  => 'toast-bottom toast-start',
            'bottom-center' => 'toast-bottom toast-center',
            'bottom-end'    => 'toast-bottom toast-end',
            default         => 'toast-top toast-end'
        };
    }
}
