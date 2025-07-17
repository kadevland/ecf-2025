@props([
    'title' => null,
    'description' => null
])

@if($title)
    <div class="bg-base-100 px-6 py-4 border-b">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-base-content">{{ $title }}</h1>
                @if($description)
                    <p class="text-base-content/70 mt-1">{{ $description }}</p>
                @endif
            </div>

            @isset($actions)
                <div class="flex gap-2">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    </div>
@endif
