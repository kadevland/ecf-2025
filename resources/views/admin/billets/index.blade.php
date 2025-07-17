<x-admin.layout>
    <x-slot name="title">{{ $viewPage->title }}</x-slot>
    <x-slot name="breadcrumbs">
        <x-admin.breadcrumbs :breadcrumbs="$viewPage->breadcrumbs" />
    </x-slot>

    <div class="space-y-6">
        <!-- En-tête avec titre et statistiques -->
        <x-admin.page-header>
            <x-slot name="title">{{ $viewPage->title }}</x-slot>
            <x-slot name="subtitle">
                @if($viewPage->billetList->isEmpty())
                    Aucun billet trouvé
                @else
                    {{ $viewPage->billetList->count() }} billet(s) trouvé(s)
                @endif
            </x-slot>
        </x-admin.page-header>

        <!-- Formulaire de recherche -->
        <x-admin.form-card>
            <x-slot name="title">Rechercher des billets</x-slot>
            @include('admin.billets.partials.search-form')
        </x-admin.form-card>

        <!-- Liste des billets -->
        @if(!$viewPage->billetList->isEmpty())
            <x-admin.listing-table>
                @include('admin.billets.partials.billets-table')
            </x-admin.listing-table>
        @else
            <div class="card">
                <div class="card-body text-center">
                    <p class="text-gray-500">Aucun billet ne correspond aux critères de recherche.</p>
                    <a href="{{ $viewPage->billetList->resetUrl() }}" class="btn btn-primary mt-4">
                        Afficher tous les billets
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-admin.layout>