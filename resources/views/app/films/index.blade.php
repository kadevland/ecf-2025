<x-app.layout title="Films à l'affiche">
    <div class="min-h-screen bg-base-200">
        <!-- Hero Section -->
        <div class="hero bg-gradient-to-r from-primary to-primary-focus text-primary-content relative overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute transform rotate-12 translate-x-1/3 translate-y-1/4">
                    <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24">
                        <path d="m18.378 16.406-1.484-3.883c-1.071.052-2.205.085-3.394.085s-2.323-.033-3.394-.085l-1.484 3.883a1.001 1.001 0 01-1.847-.706l1.342-3.509C6.42 11.732 5.828 11.249 5.828 10.659V7.5c0-1.038 1.203-1.875 2.687-1.875h6.97c1.484 0 2.687.837 2.687 1.875v3.159c0 .59-.592 1.073-1.269 1.532l1.342 3.509a1.001 1.001 0 01-1.847.706z"/>
                    </svg>
                </div>
            </div>
            
            <div class="hero-content text-center py-16 relative z-10">
                <div class="max-w-2xl">
                    <h1 class="text-5xl md:text-6xl font-bold mb-4">Films à l'affiche</h1>
                    <p class="text-xl md:text-2xl mb-6 opacity-90">
                        {{ $films->count() }} films actuellement en salle
                    </p>
                    <p class="text-lg opacity-75 max-w-lg mx-auto">
                        Programmation du {{ $periode_debut }} au {{ $periode_fin }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="container mx-auto px-4 py-8">
            <div class="bg-base-100 rounded-lg shadow-lg p-6 mb-8">
                <form method="GET" action="{{ route('films.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Recherche -->
                        <div class="lg:col-span-1">
                            <label class="label">
                                <span class="label-text">Rechercher</span>
                            </label>
                            <input type="text" 
                                   name="recherche"
                                   value="{{ request('recherche') }}"
                                   placeholder="Titre, réalisateur..." 
                                   class="input input-bordered w-full">
                        </div>
                        
                        <!-- Catégorie -->
                        <div>
                            <label class="label">
                                <span class="label-text">Catégorie</span>
                            </label>
                            <select name="categorie" class="select select-bordered w-full">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('categorie') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Tri -->
                        <div>
                            <label class="label">
                                <span class="label-text">Trier par</span>
                            </label>
                            <select name="tri" class="select select-bordered w-full">
                                <option value="titre_asc" {{ request('tri', 'titre_asc') == 'titre_asc' ? 'selected' : '' }}>Titre (A-Z)</option>
                                <option value="titre_desc" {{ request('tri') == 'titre_desc' ? 'selected' : '' }}>Titre (Z-A)</option>
                                <option value="note_desc" {{ request('tri') == 'note_desc' ? 'selected' : '' }}>Note (meilleures)</option>
                                <option value="note_asc" {{ request('tri') == 'note_asc' ? 'selected' : '' }}>Note (moins bonnes)</option>
                                <option value="date_desc" {{ request('tri') == 'date_desc' ? 'selected' : '' }}>Plus récents</option>
                                <option value="date_asc" {{ request('tri') == 'date_asc' ? 'selected' : '' }}>Plus anciens</option>
                            </select>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Rechercher
                            </button>
                            @if(request()->hasAny(['recherche', 'categorie', 'tri']))
                            <a href="{{ route('films.index') }}" class="btn btn-ghost">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Grille de films -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($films as $film)
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow">
                    <figure class="relative">
                        <img src="{{ $film['affiche'] }}" alt="{{ $film['titre'] }}" class="w-full h-96 object-cover" />
                        @if($film['note_moyenne'])
                        <div class="absolute top-2 right-2 badge badge-primary">
                            <svg class="w-4 h-4 mr-1 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            {{ number_format($film['note_moyenne'], 1) }}
                        </div>
                        @endif
                        @if(!$film['a_des_seances'])
                        <div class="absolute top-2 left-2 badge badge-warning">
                            Bientôt
                        </div>
                        @endif
                    </figure>
                    <div class="card-body">
                        <h2 class="card-title text-xl">{{ $film['titre'] }}</h2>
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span class="badge badge-outline">{{ $film['categorie'] }}</span>
                            <span class="badge badge-outline">{{ $film['duree'] }}</span>
                            @if($film['date_sortie'])
                            <span class="badge badge-outline">{{ $film['date_sortie'] }}</span>
                            @endif
                        </div>
                        
                        @if($film['description'])
                        <p class="text-sm text-base-content/70 line-clamp-3">{{ $film['description'] }}</p>
                        @endif
                        
                        @if($film['realisateur'])
                        <div class="text-sm text-base-content/60 mt-2">
                            <p>Réalisé par {{ $film['realisateur'] }}</p>
                        </div>
                        @endif
                        
                        @if($film['a_des_seances'])
                        <!-- Séances de la semaine -->
                        <div class="mt-4">
                            <p class="text-sm font-semibold mb-2">Séances cette semaine :</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($film['seances_periode']->take(4) as $seance)
                                <span class="badge badge-sm badge-ghost">{{ $seance['date'] }} {{ $seance['heure'] }}</span>
                                @endforeach
                                @if($film['seances_periode']->count() > 4)
                                <span class="badge badge-sm badge-info">+{{ $film['seances_periode']->count() - 4 }}</span>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        <div class="card-actions justify-between items-center mt-4">
                            <div class="text-xs text-base-content/50">
                                @if($film['a_des_seances'])
                                {{ $film['seances_periode']->count() }} séance(s)
                                @else
                                Aucune séance programmée
                                @endif
                            </div>
                            <div class="flex gap-2">
                                @if($film['a_des_seances'])
                                <a href="{{ route('films.show', $film['model']) }}" class="btn btn-primary btn-sm">
                                    Voir les séances
                                </a>
                                @endif
                                <a href="{{ route('films.show', $film['model']) }}" class="btn btn-ghost btn-sm">
                                    En savoir plus
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-12">
                {{ $films->links('pagination.daisyui') }}
            </div>
            
            <!-- Message aucun résultat -->
            @if($films->isEmpty())
            <div class="text-center py-12">
                <div class="max-w-md mx-auto">
                    <svg class="w-24 h-24 mx-auto text-base-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2C7 1.45 7.45 1 8 1h8c.55 0 1 .45 1 1v2h5c.55 0 1 .45 1 1s-.45 1-1 1h-1v14c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V6H2c-.55 0-1-.45-1-1s.45-1 1-1h5z"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-base-content/70 mb-2">Aucun film trouvé</h3>
                    <p class="text-base-content/50">
                        Aucun film n'est programmé pour cette période
                    </p>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app.layout>