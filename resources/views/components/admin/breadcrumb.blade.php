@if(count($items) > 0)
<div class="bg-base-100 px-6 py-3 border-b">
    <div class="breadcrumbs text-sm">
        <ul>
            @foreach ($items as $item)
                <li>
                    @if ($item['href'] && !$item['current'])
                        <a href="{{ $item['href'] }}" class="hover:text-primary">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="text-base-content/70">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif