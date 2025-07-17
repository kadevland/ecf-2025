@props(['columns' => [], 'rows' => [], 'sortable' => true, 'searchable' => true, 'paginated' => true, 'currentSort' => '', 'currentDirection' => 'asc', 'searchQuery' => '', 'pagination' => null, 'actions' => []])

<div class="bg-base-100 rounded-lg shadow-sm">
    {{-- En-tête avec recherche et actions --}}
    @if($searchable || !empty($actions))
        <div class="p-4 border-b border-base-300">
            <div class="flex justify-between items-center gap-4">
                {{-- Recherche --}}
                @if($searchable)
                    <div class="flex-1 max-w-md">
                        <form method="GET" class="flex gap-2">
                            {{-- Conserver les paramètres de tri --}}
                            @if($currentSort)
                                <input type="hidden" name="sort" value="{{ $currentSort }}">
                                <input type="hidden" name="direction" value="{{ $currentDirection }}">
                            @endif
                            
                            <div class="join flex-1">
                                <input 
                                    type="text" 
                                    name="search" 
                                    value="{{ $searchQuery }}"
                                    placeholder="Rechercher..." 
                                    class="input input-bordered join-item flex-1"
                                >
                                <button type="submit" class="btn btn-primary join-item">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            {{-- Bouton reset --}}
                            @if($searchQuery)
                                <a href="{{ request()->url() }}" class="btn btn-ghost">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            @endif
                        </form>
                    </div>
                @endif

                {{-- Actions --}}
                @if(!empty($actions))
                    <div class="flex gap-2">
                        @foreach($actions as $action)
                            <a 
                                href="{{ $action['url'] }}" 
                                class="btn {{ $action['class'] ?? 'btn-primary' }}"
                                @if(isset($action['confirm']))
                                    onclick="return confirm('{{ $action['confirm'] }}')"
                                @endif
                            >
                                @if(isset($action['icon']))
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"></path>
                                    </svg>
                                @endif
                                {{ $action['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="table table-zebra">
            <thead>
                <tr>
                    @foreach($columns as $key => $column)
                        <th class="{{ $column['class'] ?? '' }}">
                            @if($sortable && ($column['sortable'] ?? true))
                                <a 
                                    href="{{ $sortUrl($key) }}" 
                                    class="flex items-center gap-2 hover:text-primary transition-colors {{ $sortClass($key) }}"
                                >
                                    {{ $column['label'] }}
                                    <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortIcon($key) }}"></path>
                                    </svg>
                                </a>
                            @else
                                {{ $column['label'] }}
                            @endif
                        </th>
                    @endforeach
                    @if(!empty($columns) && isset($columns[array_key_first($columns)]['actions']))
                        <th class="text-right">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr class="hover">
                        @foreach($columns as $key => $column)
                            <td class="{{ $column['class'] ?? '' }}">
                                @if(isset($column['format']))
                                    @switch($column['format'])
                                        @case('badge')
                                            <div class="badge {{ $row[$key . '_class'] ?? 'badge-neutral' }}">
                                                {{ $row[$key] }}
                                            </div>
                                            @break
                                        @case('date')
                                            {{ $row[$key] ? \Carbon\CarbonImmutable::parse($row[$key])->format('d/m/Y') : '-' }}
                                            @break
                                        @case('datetime')
                                            {{ $row[$key] ? \Carbon\CarbonImmutable::parse($row[$key])->format('d/m/Y H:i') : '-' }}
                                            @break
                                        @case('currency')
                                            {{ number_format($row[$key], 2, ',', ' ') }} €
                                            @break
                                        @case('boolean')
                                            @if($row[$key])
                                                <div class="badge badge-success">Oui</div>
                                            @else
                                                <div class="badge badge-error">Non</div>
                                            @endif
                                            @break
                                        @case('image')
                                            @if($row[$key])
                                                <div class="avatar">
                                                    <div class="w-12 h-12 rounded">
                                                        <img src="{{ $row[$key] }}" alt="Image" class="object-cover">
                                                    </div>
                                                </div>
                                            @else
                                                <div class="w-12 h-12 bg-base-300 rounded flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-base-content/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            @break
                                        @default
                                            {{ $row[$key] ?? '-' }}
                                    @endswitch
                                @else
                                    {{ $row[$key] ?? '-' }}
                                @endif
                            </td>
                        @endforeach
                        
                        {{-- Actions de ligne --}}
                        @if(isset($row['actions']))
                            <td class="text-right">
                                <div class="dropdown dropdown-end">
                                    <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                    </div>
                                    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow">
                                        @foreach($row['actions'] as $action)
                                            <li>
                                                <a 
                                                    href="{{ $action['url'] }}" 
                                                    class="{{ $action['class'] ?? '' }}"
                                                    @if(isset($action['confirm']))
                                                        onclick="return confirm('{{ $action['confirm'] }}')"
                                                    @endif
                                                >
                                                    @if(isset($action['icon']))
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"></path>
                                                        </svg>
                                                    @endif
                                                    {{ $action['label'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + (isset($columns[array_key_first($columns)]['actions']) ? 1 : 0) }}" class="text-center py-8">
                            <div class="text-base-content/60">
                                @if($searchQuery)
                                    Aucun résultat pour "{{ $searchQuery }}"
                                @else
                                    Aucune donnée disponible
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($paginated && $pagination)
        <div class="p-4 border-t border-base-300">
            <div class="flex justify-between items-center">
                <div class="text-sm text-base-content/60">
                    Affichage de {{ $pagination->firstItem() ?? 0 }} à {{ $pagination->lastItem() ?? 0 }} 
                    sur {{ $pagination->total }} résultats
                </div>
                
                @if($pagination->hasPages())
                    <div class="join">
                        {{-- Précédent --}}
                        @if($pagination->previousPageUrl())
                            <a href="{{ $pagination->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @else
                            <span class="join-item btn btn-sm btn-disabled">«</span>
                        @endif

                        {{-- Pages --}}
                        @foreach($pagination->getUrlRange(1, $pagination->lastPage()) as $page => $url)
                            @if($page == $pagination->currentPage())
                                <span class="join-item btn btn-sm btn-active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="join-item btn btn-sm">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Suivant --}}
                        @if($pagination->nextPageUrl())
                            <a href="{{ $pagination->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <span class="join-item btn btn-sm btn-disabled">»</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
.sort-asc svg {
    @apply text-primary opacity-100;
}
.sort-desc svg {
    @apply text-primary opacity-100;
}
</style>