<x-admin.layout title="Gestion des Salles">
    <!-- Breadcrumbs -->
    <x-admin.breadcrumbs :breadcrumbs="$viewPage->breadcrumbs" />

    <div class="space-y-6 mt-6">
        <!-- Formulaire de recherche personnalisé -->
        <div class="bg-base-100 rounded-lg shadow-sm p-4">
            <x-admin.salle.search-form
                :searchForm="$viewPage->searchForm" />
        </div>

        <!-- Table des salles sans formulaire de recherche -->
        <!-- Liste des salles -->
        <x-admin.table.list :listElement="$viewPage->salleList">
            @foreach($viewPage->salleList->items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $item->displayValue('numero') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->displayValue('nom') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->displayValue('capacite') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $item->salle->classeBadgeEtat() }}">
                            {{ $item->displayValue('etat') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->displayValue('qualite_projection') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $viewPage->cinema }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <x-admin.table.action-list :actionList="$item->actions" />
                    </td>
                </tr>
            @endforeach
        </x-admin.table.list>
    </div>
</x-admin.layout>
