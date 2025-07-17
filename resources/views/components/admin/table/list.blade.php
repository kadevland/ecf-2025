@props(['listElement'])

<div class="space-y-4">
    {{-- Titre et actions globales --}}
    @if($listElement->title || $listElement->hasActions())
        <div class="flex items-center justify-between">
            @if($listElement->title)
                <h2 class="text-lg font-semibold text-gray-900">{{ $listElement->title }}</h2>
            @endif

            @if($listElement->hasActions())
                <div class="flex items-center space-x-2">
                    <x-admin.table.action-list :actionList="$listElement->actions" />
                </div>
            @endif
        </div>
    @endif

    {{-- Tableau --}}
    @if($listElement->hasItems())
        <div class="shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                {{-- En-têtes --}}
                @if($listElement->hasHeaders())
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach($listElement->headers as $header)
                                <x-admin.table.header-cell :header="$header" />
                            @endforeach
                            {{-- Colonne actions si au moins un item a des actions --}}
                            @if($listElement->items->some(fn($item) => $item->hasActions()))
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            @endif
                        </tr>
                    </thead>
                @endif

                {{-- Corps du tableau --}}
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(isset($slot))
                        {{ $slot }}
                    @else
                        {{-- Fallback par défaut si pas de slot --}}
                        @foreach($listElement->items as $item)
                            <tr class="hover:bg-gray-50">
                                @foreach($listElement->headers as $header)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->displayValue($header->key) }}
                                    </td>
                                @endforeach

                                @if($item->hasActions())
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-admin.table.action-list :actionList="$item->actions" />
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    @else
        {{-- État vide --}}
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8V9a1 1 0 01-1 1H7a1 1 0 01-1-1V5"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun élément</h3>
            <p class="mt-1 text-sm text-gray-500">Aucun élément à afficher pour le moment.</p>
        </div>
    @endif

    {{-- Pagination --}}
    @if($listElement->hasPagination())
        <div class="mt-4">
            {!! $listElement->pagination->links() !!}
        </div>
    @endif
</div>
