@props(['breadcrumbs' => null])

@if ($breadcrumbs)
    <div class="bg-base-100 px-6 py-3 border-b">
        <div class="breadcrumbs text-sm">
            <ul>
                @foreach ($breadcrumbs as $breadcrumb)
                    <li>

                        @php
                            $href = $breadcrumb['href'] ?? ($breadcrumb['url'] ?? false);
                        @endphp

                        @if ($href)
                            <a href="{{ $href }}" class="hover:text-primary">
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
@endif
