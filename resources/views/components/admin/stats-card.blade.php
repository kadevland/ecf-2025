@props([
    'title' => 'Statistique',
    'value' => 0,
    'icon' => 'fas fa-chart-bar',
    'color' => 'blue',
    'trend' => null, // 'up', 'down', 'stable'
    'trendValue' => null,
    'link' => null
])

@php
    $colorClasses = [
        'blue' => 'bg-blue-500',
        'green' => 'bg-green-500',
        'red' => 'bg-red-500',
        'yellow' => 'bg-yellow-500',
        'purple' => 'bg-purple-500',
        'pink' => 'bg-pink-500',
        'indigo' => 'bg-indigo-500',
        'gray' => 'bg-gray-500'
    ];
    
    $trendClasses = [
        'up' => 'text-green-600',
        'down' => 'text-red-600',
        'stable' => 'text-gray-600'
    ];
    
    $trendIcons = [
        'up' => 'fas fa-arrow-up',
        'down' => 'fas fa-arrow-down',
        'stable' => 'fas fa-minus'
    ];
@endphp

<div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                {{ $title }}
            </h3>
            <p class="mt-2 text-3xl font-bold text-gray-900">
                {{ $value }}
            </p>
            
            @if($trend && $trendValue)
                <div class="mt-2 flex items-center text-sm">
                    <i class="{{ $trendIcons[$trend] }} mr-1 {{ $trendClasses[$trend] }}"></i>
                    <span class="{{ $trendClasses[$trend] }}">{{ $trendValue }}</span>
                    <span class="text-gray-500 ml-2">vs période précédente</span>
                </div>
            @endif
        </div>
        
        <div class="flex-shrink-0">
            <div class="w-12 h-12 {{ $colorClasses[$color] }} rounded-lg flex items-center justify-center">
                <i class="{{ $icon }} text-white text-xl"></i>
            </div>
        </div>
    </div>
    
    @if($link)
        <div class="mt-4 pt-4 border-t border-gray-200">
            <a href="{{ $link }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Voir les détails →
            </a>
        </div>
    @endif
</div>