<form method="GET" action="{{ route('gestion.supervision.billets.index') }}" class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Recherche générale -->
        <div>
            <label for="recherche" class="form-label">Recherche</label>
            <input 
                type="text" 
                id="recherche" 
                name="recherche" 
                value="{{ $viewPage->searchForm->recherche }}"
                placeholder="Numéro de billet, place..."
                class="form-input"
            >
        </div>

        <!-- Type de tarif -->
        <div>
            <label for="typeTarif" class="form-label">Type de tarif</label>
            <select id="typeTarif" name="typeTarif" class="form-select">
                @foreach($viewPage->searchForm->typeTarifOptions() as $value => $label)
                    <option value="{{ $value }}" {{ $viewPage->searchForm->typeTarif === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Statut d'utilisation -->
        <div>
            <label for="utilise" class="form-label">Statut</label>
            <select id="utilise" name="utilise" class="form-select">
                @foreach($viewPage->searchForm->utiliseOptions() as $value => $label)
                    <option value="{{ $value }}" {{ $viewPage->searchForm->getUtiliseValue() === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex justify-between items-center">
        <div class="flex space-x-2">
            <button type="submit" class="btn btn-primary">
                Rechercher
            </button>
            <a href="{{ $viewPage->billetList->resetUrl() }}" class="btn btn-outline">
                Réinitialiser
            </a>
        </div>

        <!-- Nombre par page -->
        <div class="flex items-center space-x-2">
            <label for="perPage" class="text-sm">Affichage :</label>
            <select id="perPage" name="perPage" class="form-select w-auto" onchange="this.form.submit()">
                @foreach($viewPage->searchForm->perPageOptions() as $value => $label)
                    <option value="{{ $value }}" {{ $viewPage->searchForm->perPage === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</form>