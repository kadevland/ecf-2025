<x-app.layout title="Nos Cinémas">
    <div class="min-h-screen bg-base-200">
        <!-- Hero Section -->
        <div class="hero bg-gradient-to-r from-primary to-primary-focus text-primary-content relative overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute transform rotate-12 translate-x-1/4 translate-y-1/4">
                    <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18 3v2h-2V3H8v2H6V3H4v18h2v-2h2v2h8v-2h2v2h2V3h-2zM8 17H6v-2h2v2zm0-4H6v-2h2v2zm0-4H6V7h2v2zm10 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V7h2v2z"/>
                    </svg>
                </div>
            </div>
            
            <div class="hero-content text-center py-16 relative z-10">
                <div class="max-w-2xl">
                    <h1 class="text-5xl md:text-6xl font-bold mb-4">Nos Cinémas</h1>
                    <p class="text-xl md:text-2xl mb-6 opacity-90">
                        {{ $cinemas->count() }} cinémas en France et en Belgique
                    </p>
                    <p class="text-lg opacity-75 max-w-lg mx-auto">
                        Des salles modernes équipées des dernières technologies pour vivre 
                        une expérience cinématographique inoubliable
                    </p>
                    
                    <!-- Stats rapides -->
                    <div class="flex justify-center gap-12 mt-8">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-content">{{ $cinemas->count() }}</div>
                            <div class="text-sm text-primary-content/70 uppercase tracking-wider">Cinémas</div>
                        </div>
                        <div class="w-px h-12 bg-primary-content/20"></div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-content">{{ $cinemas->sum('nombre_salles') }}</div>
                            <div class="text-sm text-primary-content/70 uppercase tracking-wider">Salles</div>
                        </div>
                        <div class="w-px h-12 bg-primary-content/20"></div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-content">{{ $cinemasByCountry->count() }}</div>
                            <div class="text-sm text-primary-content/70 uppercase tracking-wider">Pays</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des cinémas -->
        <div class="container mx-auto px-4 py-8">
            
            <!-- Barre de recherche et filtres -->
            <div class="bg-base-100 rounded-lg shadow-lg p-6 mb-8">
                <form method="GET" action="{{ route('cinemas.index') }}" class="flex flex-col gap-4">
                    <!-- Barre de recherche -->
                    <div class="flex flex-col lg:flex-row gap-4 items-center">
                        <div class="w-full lg:w-1/2">
                            <div class="relative">
                                <input type="text" 
                                       name="search"
                                       value="{{ $search }}"
                                       placeholder="Rechercher un cinéma par ville ou nom..." 
                                       class="input input-bordered w-full pr-12">
                                <button type="submit" class="btn btn-ghost btn-sm absolute right-2 top-1/2 -translate-y-1/2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Filtres par pays -->
                        <div class="flex flex-wrap justify-center gap-2">
                            <a href="{{ route('cinemas.index', ['search' => $search]) }}" 
                               class="btn btn-sm {{ !$selectedPays || $selectedPays === 'tous' ? 'btn-primary' : 'btn-outline' }}">
                                Tous ({{ $allCinemasCount }})
                            </a>
                            @foreach($cinemasByCountry as $pays => $cinemasCountry)
                            <a href="{{ route('cinemas.index', ['pays' => $pays, 'search' => $search]) }}" 
                               class="btn btn-sm {{ $selectedPays === $pays ? 'btn-primary' : 'btn-outline' }}">
                                {{ $pays }} ({{ $cinemasCountry->count() }})
                            </a>
                            @endforeach
                        </div>
                    </div>
                </form>
                
                <!-- Clear filters -->
                @if($selectedPays || $search)
                <div class="flex justify-center mt-4">
                    <a href="{{ route('cinemas.index') }}" class="btn btn-ghost btn-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Effacer les filtres
                    </a>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($cinemas as $cinema)
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow">
                    <figure>
                        <img src="{{ $cinema['photo'] }}" alt="{{ $cinema['nom'] }}" class="w-full h-64 object-cover" />
                    </figure>
                    <div class="card-body">
                        <h2 class="card-title text-2xl">{{ $cinema['nom'] }}</h2>
                        
                        @if($cinema['description'])
                        <p class="text-sm text-base-content/70 mb-3">{{ $cinema['description'] }}</p>
                        @endif
                        
                        <!-- Adresse -->
                        <div class="space-y-2 text-sm">
                            <p class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>
                                    {{ $cinema['adresse'] }}<br>
                                    <span class="font-semibold">{{ $cinema['code_postal'] }} {{ $cinema['ville'] }}</span>, {{ $cinema['pays'] }}
                                </span>
                            </p>
                            
                            <p class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <a href="tel:{{ $cinema['telephone'] }}" class="hover:text-primary transition-colors">
                                    {{ $cinema['telephone'] }}
                                </a>
                            </p>
                            
                            <p class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <a href="mailto:{{ $cinema['email'] }}" class="hover:text-primary transition-colors">
                                    {{ $cinema['email'] }}
                                </a>
                            </p>
                            
                            <p class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $cinema['horaires'] }}
                            </p>
                        </div>

                        <!-- Services disponibles -->
                        <div class="flex flex-wrap gap-2 mt-4">
                            <div class="badge badge-lg badge-outline">{{ $cinema['nombre_salles'] }} salles</div>
                            @if($cinema['parking'])
                                <div class="badge badge-lg badge-success badge-outline">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-1"/>
                                    </svg>
                                    Parking
                                </div>
                            @endif
                            @if($cinema['acces_pmr'])
                                <div class="badge badge-lg badge-info badge-outline">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Accès PMR
                                </div>
                            @endif
                            @if($cinema['restaurant'])
                                <div class="badge badge-lg badge-warning badge-outline">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                    </svg>
                                    Restaurant
                                </div>
                            @endif
                            @if($cinema['bar'])
                                <div class="badge badge-lg badge-secondary badge-outline">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Bar
                                </div>
                            @endif
                            @if($cinema['boutique'])
                                <div class="badge badge-lg badge-accent badge-outline">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6L5 9z"/>
                                    </svg>
                                    Boutique
                                </div>
                            @endif
                        </div>
                        
                        <div class="card-actions justify-between items-center mt-6">
                            <button class="btn btn-sm btn-outline btn-primary" 
                                    onclick="window.open('https://maps.google.com?q={{ $cinema['coordonnees']['latitude'] }},{{ $cinema['coordonnees']['longitude'] }}', '_blank')">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Itinéraire
                            </button>
                            <a href="{{ route('cinemas.show', $cinema['model']) }}" class="btn btn-primary">
                                Voir les séances
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Message aucun résultat -->
            @if($cinemas->isEmpty())
            <div class="text-center py-12">
                <div class="max-w-md mx-auto">
                    <svg class="w-24 h-24 mx-auto text-base-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.562M15 17H9v-2h6v2z"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-base-content/70 mb-2">Aucun cinéma trouvé</h3>
                    <p class="text-base-content/50">
                        Essayez de modifier vos filtres ou votre recherche
                    </p>
                    <a href="{{ route('cinemas.index') }}" class="btn btn-primary btn-sm mt-4">
                        Voir tous les cinémas
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app.layout>