@props([
    'title' => 'Liste',
    'items' => [],
    'columns' => [],
    'actions' => [],
    'route' => null,
    'searchable' => true,
    'filterable' => false,
    'perPage' => 10,
    'createRoute' => null,
    'createLabel' => 'Créer'
])

<div class="bg-white rounded-lg shadow-md p-6" x-data="listingTable">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ $title }}</h2>
        
        @if($createRoute)
            <a href="{{ $createRoute }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>{{ $createLabel }}
            </a>
        @endif
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 flex flex-col md:flex-row gap-4">
        @if($searchable)
            <div class="flex-1">
                <input type="text" 
                       x-model="search" 
                       placeholder="Rechercher..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        @endif
        
        @if($filterable)
            <div class="flex gap-2">
                <select x-model="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                </select>
            </div>
        @endif
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($columns as $column)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if($column['sortable'] ?? false)
                                <button @click="sortBy('{{ $column['key'] }}')" 
                                        class="flex items-center hover:text-gray-700">
                                    {{ $column['label'] }}
                                    <i class="fas fa-sort ml-1 text-gray-400"></i>
                                </button>
                            @else
                                {{ $column['label'] }}
                            @endif
                        </th>
                    @endforeach
                    @if(!empty($actions))
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50">
                        @foreach($columns as $column)
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($column['type'] === 'badge')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->getBadgeClass($column['key']) }}">
                                        {{ $item->getBadgeText($column['key']) }}
                                    </span>
                                @elseif($column['type'] === 'date')
                                    <span class="text-gray-900">{{ $item->{$column['key']}->format('d/m/Y H:i') }}</span>
                                @elseif($column['type'] === 'currency')
                                    <span class="text-gray-900">{{ number_format($item->{$column['key']}, 2) }} €</span>
                                @elseif($column['type'] === 'boolean')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->{$column['key']} ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $item->{$column['key']} ? 'Oui' : 'Non' }}
                                    </span>
                                @elseif($column['type'] === 'truncate')
                                    <span class="text-gray-900" title="{{ $item->{$column['key']} }}">
                                        {{ Str::limit($item->{$column['key']}, 50) }}
                                    </span>
                                @else
                                    <span class="text-gray-900">{{ $item->{$column['key']} }}</span>
                                @endif
                            </td>
                        @endforeach
                        
                        @if(!empty($actions))
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @foreach($actions as $action)
                                        @if($action['type'] === 'view')
                                            <a href="{{ route($action['route'], $item->id) }}" 
                                               class="text-blue-600 hover:text-blue-900" 
                                               title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @elseif($action['type'] === 'edit')
                                            <a href="{{ route($action['route'], $item->id) }}" 
                                               class="text-yellow-600 hover:text-yellow-900" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @elseif($action['type'] === 'delete')
                                            <button @click="confirmDelete('{{ $item->id }}')" 
                                                    class="text-red-600 hover:text-red-900" 
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + (!empty($actions) ? 1 : 0) }}" 
                            class="px-6 py-4 text-center text-gray-500">
                            Aucun élément trouvé
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $items->links() }}
        </div>
    @endif
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('listingTable', () => ({
        search: '',
        statusFilter: '',
        sortField: '',
        sortDirection: 'asc',
        
        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'asc';
            }
            this.applyFilters();
        },
        
        applyFilters() {
            const params = new URLSearchParams(window.location.search);
            
            if (this.search) {
                params.set('search', this.search);
            } else {
                params.delete('search');
            }
            
            if (this.statusFilter) {
                params.set('status', this.statusFilter);
            } else {
                params.delete('status');
            }
            
            if (this.sortField) {
                params.set('sort', this.sortField);
                params.set('direction', this.sortDirection);
            } else {
                params.delete('sort');
                params.delete('direction');
            }
            
            window.location.search = params.toString();
        },
        
        confirmDelete(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                // Implement delete logic
                console.log('Delete', id);
            }
        }
    }))
})
</script>