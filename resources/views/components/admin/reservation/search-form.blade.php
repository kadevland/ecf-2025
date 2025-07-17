@props(['searchForm'])

<form method="GET" class="space-y-4">
    {{-- Les paramètres de tri sont conservés automatiquement par le formulaire GET --}}
    
    <div class="flex flex-wrap gap-4">
        {{-- Recherche textuelle --}}
        <div class="flex-1 min-w-[200px]">
            <div class="join w-full">
                <input 
                    type="text" 
                    name="recherche" 
                    value="{{ $searchForm->recherche }}"
                    placeholder="Rechercher une réservation..." 
                    class="input input-bordered join-item flex-1"
                >
                <button type="submit" class="btn btn-primary join-item">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Statut réservation --}}
        <div class="form-control">
            <select name="statut" class="select select-bordered">
                <option value="">Tous les statuts</option>
                <option value="en_attente" @selected($searchForm->statut === 'en_attente')>En attente</option>
                <option value="confirmee" @selected($searchForm->statut === 'confirmee')>Confirmée</option>
                <option value="payee" @selected($searchForm->statut === 'payee')>Payée</option>
                <option value="annulee" @selected($searchForm->statut === 'annulee')>Annulée</option>
                <option value="terminee" @selected($searchForm->statut === 'terminee')>Terminée</option>
                <option value="expiree" @selected($searchForm->statut === 'expiree')>Expirée</option>
            </select>
        </div>

        {{-- Nombre par page --}}
        <div class="form-control">
            <select name="perPage" class="select select-bordered">
                @foreach($searchForm->perPageOptions() as $option)
                    <option value="{{ $option }}" @selected($searchForm->perPage == $option)>
                        {{ $option }} par page
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Bouton reset --}}
        @if($searchForm->isNotEmpty())
            <a href="?" class="btn btn-ghost">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Réinitialiser
            </a>
        @endif
    </div>
</form>