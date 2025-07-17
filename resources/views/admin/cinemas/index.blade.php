<x-admin.layout title="Gestion des Cinémas">
    <!-- Breadcrumbs -->
    <x-admin.breadcrumbs :breadcrumbs="$viewPage->breadcrumbs" />
    <div class="space-y-6 mt-6">
        <!-- Formulaire de recherche personnalisé -->
        <div class="bg-base-100 rounded-lg shadow-sm p-4">
            <x-admin.cinema.search-form
                :searchForm="$viewPage->searchForm" />
        </div>

        <!-- Table des cinémas sans formulaire de recherche -->
        <!-- Liste des cinémas -->
        <x-admin.table.list :listElement="$viewPage->cinemaList">
            @foreach($viewPage->cinemaList->items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $item->displayValue('nom') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->displayValue('ville') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->displayValue('pays') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $item->displayValue('classeBadgeStatut') }}">
                            {{ $item->displayValue('statut') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->displayValue('nombreSalles') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->displayValue('code') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <x-admin.table.action-list :actionList="$item->actions" />
                    </td>
                </tr>
            @endforeach
        </x-admin.table.list>
    </div>
</x-admin.layout>

