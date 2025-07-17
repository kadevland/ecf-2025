@props(['breadcrumbs' => null])

@if ($breadcrumbs)
    <div class="bg-base-200 px-6 py-3">
        <div class="container mx-auto">
            <div class="breadcrumbs text-sm">
                <ul>
                    @foreach ($breadcrumbs as $breadcrumb)
                        <li>
                            @if ($breadcrumb['href'] ?? false)
                                <a href="{{ $breadcrumb['href'] }}" class="link link-hover">
                                    {{ $breadcrumb['label'] }}
                                </a>
                            @else
                                <span class="text-base-content/70">{{ $breadcrumb['label'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
