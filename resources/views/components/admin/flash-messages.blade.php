@if (session()->has(['success', 'error', 'warning', 'info']))
    <div class="mx-6 mt-4 space-y-3">
        {{-- Success message --}}
        @if (session('success'))
            <div class="alert alert-success" x-data="{ show: true }" x-show="show" x-transition>
                <x-lucide-check-circle class="w-5 h-5" />
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="btn btn-ghost btn-sm btn-circle ml-auto">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        @endif

        {{-- Error message --}}
        @if (session('error'))
            <div class="alert alert-error" x-data="{ show: true }" x-show="show" x-transition>
                <x-lucide-x-circle class="w-5 h-5" />
                <span>{{ session('error') }}</span>
                <button @click="show = false" class="btn btn-ghost btn-sm btn-circle ml-auto">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        @endif

        {{-- Warning message --}}
        @if (session('warning'))
            <div class="alert alert-warning" x-data="{ show: true }" x-show="show" x-transition>
                <x-lucide-alert-triangle class="w-5 h-5" />
                <span>{{ session('warning') }}</span>
                <button @click="show = false" class="btn btn-ghost btn-sm btn-circle ml-auto">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        @endif

        {{-- Info message --}}
        @if (session('info'))
            <div class="alert alert-info" x-data="{ show: true }" x-show="show" x-transition>
                <x-lucide-info class="w-5 h-5" />
                <span>{{ session('info') }}</span>
                <button @click="show = false" class="btn btn-ghost btn-sm btn-circle ml-auto">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        @endif
    </div>
@endif
