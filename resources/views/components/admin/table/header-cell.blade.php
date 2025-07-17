@props(['header'])

<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
    @if($header->sortable)
        <a href="{{ $header->sortUrl() }}" 
           class="group inline-flex items-center space-x-1 hover:text-gray-700 transition-colors">
            <span>{{ $header->label }}</span>
            <svg class="w-4 h-4 transition-colors group-hover:text-gray-600 {{ $header->sortIconClass() }}" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2" 
                      d="{{ $header->sortIcon() }}">
                </path>
            </svg>
        </a>
    @else
        <span>{{ $header->label }}</span>
    @endif
</th>