@if (session()->has(['success', 'error', 'warning', 'info']))
    <div class="mx-6 mt-4 space-y-3">

        {{-- Success message --}}
        @if (session('success'))
            <div class="container mx-auto px-6 pt-4">
                <div class="alert alert-success">
                    <x-lucide-check-circle class="w-5 h-5" />
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        {{-- Error message --}}
        @if (session('error'))
            <div class="container mx-auto px-6 pt-4">
                <div class="alert alert-error">
                    <x-lucide-x-circle class="w-5 h-5" />
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Warning message --}}
        @if (session('warning'))
            <div class="container mx-auto px-6 pt-4">
                <div class="alert alert-warning">
                    <x-lucide-alert-triangle class="w-5 h-5" />
                    <span>{{ session('warning') }}</span>
                </div>
            </div>
        @endif

        {{-- Info message --}}
        @if (session('info'))
            <div class="container mx-auto px-6 pt-4">
                <div class="alert alert-info">
                    <x-lucide-info class="w-5 h-5" />
                    <span>{{ session('info') }}</span>
                </div>
            </div>
        @endif

    </div>
@endif
