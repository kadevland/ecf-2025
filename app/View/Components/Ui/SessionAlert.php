<?php

declare(strict_types=1);

namespace App\View\Components\Ui;

use Illuminate\View\Component;
use Illuminate\View\View;

final class SessionAlert extends Component
{
    public readonly ?string $message;

    public readonly string $type;

    public readonly string $classes;

    public readonly string $icon;

    public function __construct()
    {
        $this->message = $this->getSessionMessage();
        $this->type    = $this->getMessageType();
        $this->classes = $this->getClasses();
        $this->icon    = $this->getIcon();
    }

    public function shouldRender(): bool
    {
        return $this->message !== null;
    }

    public function render(): View
    {
        return view('components.ui.session-alert');
    }

    private function getSessionMessage(): ?string
    {
        // Vérifier dans l'ordre de priorité
        if (session()->has('error')) {
            return session('error');
        }

        if (session()->has('warning')) {
            return session('warning');
        }

        if (session()->has('success')) {
            return session('success');
        }

        if (session()->has('status')) {
            return session('status');
        }

        if (session()->has('info')) {
            return session('info');
        }

        return null;
    }

    private function getMessageType(): string
    {
        if (session()->has('error')) {
            return 'error';
        }
        if (session()->has('warning')) {
            return 'warning';
        }
        if (session()->has('success')) {
            return 'success';
        }
        if (session()->has('info')) {
            return 'info';
        }

        // 'status' est considéré comme success par défaut
        return 'success';
    }

    private function getClasses(): string
    {
        return match ($this->type) {
            'success' => 'alert-success',
            'warning' => 'alert-warning',
            'error'   => 'alert-error',
            default   => 'alert-info'
        };
    }

    private function getIcon(): string
    {
        return match ($this->type) {
            'success' => '<svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            'warning' => '<svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>',
            'error'   => '<svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            default   => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        };
    }
}
