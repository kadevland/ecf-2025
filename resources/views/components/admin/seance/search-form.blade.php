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
                    placeholder="Rechercher une séance..." 
                    class="input input-bordered join-item flex-1"
                >
                <button type="submit" class="btn btn-primary join-item">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- État --}}
        <div class="form-control">
            <select name="etat" class="select select-bordered">
                @foreach($searchForm->etatOptions() as $value => $label)
                    <option value="{{ $value }}" @selected($searchForm->etat == $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Qualité de projection --}}
        <div class="form-control">
            <select name="qualite_projection" class="select select-bordered">
                @foreach($searchForm->qualiteProjectionOptions() as $value => $label)
                    <option value="{{ $value }}" @selected($searchForm->qualiteProjection == $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Date début --}}
        <div class="form-control">
            <input 
                type="date" 
                name="date_debut" 
                value="{{ $searchForm->dateDebut }}"
                class="input input-bordered"
                placeholder="Date début"
            >
        </div>

        {{-- Date fin --}}
        <div class="form-control">
            <input 
                type="date" 
                name="date_fin" 
                value="{{ $searchForm->dateFin }}"
                class="input input-bordered"
                placeholder="Date fin"
            >
        </div>

        {{-- Nombre par page --}}
        <div class="form-control">
            <select name="perPage" class="select select-bordered">
                @foreach($searchForm->perPageOptions() as $value => $label)
                    <option value="{{ $value }}" @selected($searchForm->perPage == $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Bouton reset --}}
        @if($searchForm->isNotEmpty())
            <a href="{{ $searchForm->resetUrl() }}" class="btn btn-ghost">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Réinitialiser
            </a>
        @endif
    </div>
</form>