<x-admin.layout title="Gestion des Réservations">
    <!-- Breadcrumbs -->
    <x-admin.breadcrumbs :breadcrumbs="$viewPage->breadcrumbs" />

    <div class="space-y-6 mt-6">
        <!-- Formulaire de recherche personnalisé -->
        <div class="bg-base-100 rounded-lg shadow-sm p-4">
            <x-admin.reservation.search-form
                :searchForm="$viewPage->searchForm" />
        </div>

        <!-- Liste des réservations -->
        <x-admin.table.list :listElement="$viewPage->reservationList">
            @foreach($viewPage->reservationList->items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $item->displayValue('numeroReservation') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->displayValue('statut') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->displayValue('nombrePlaces') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->displayValue('prixTotal') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->displayValue('codeCinema') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->displayValue('dateCreation') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <x-admin.table.action-list :actionList="$item->actions" />
                    </td>
                </tr>
            @endforeach
        </x-admin.table.list>
    </div>
</x-admin.layout>