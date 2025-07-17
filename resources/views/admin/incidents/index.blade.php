<x-admin.layout title="Gestion des Incidents">
    <!-- Breadcrumbs -->
    <x-admin.breadcrumbs :breadcrumbs="$viewPage->breadcrumbs" />

    <div class="space-y-6 mt-6">
        <!-- Formulaire de recherche personnalisé -->
        <div class="bg-base-100 rounded-lg shadow-sm p-4">
            <x-admin.incident.search-form
                :searchForm="$viewPage->searchForm" />
        </div>

        <!-- Table des incidents sans formulaire de recherche -->
        <!-- Liste des incidents -->
        <x-admin.table.list :listElement="$viewPage->incidentList">
            @foreach($viewPage->incidentList->items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $item->displayValue('titre') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->displayValue('created_at') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <x-admin.table.action-list :actionList="$item->actions" />
                    </td>
                </tr>
            @endforeach
        </x-admin.table.list>
    </div>
</x-admin.layout>