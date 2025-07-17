<x-app.layout :title="$cinema->nom">
    <div class="min-h-screen bg-base-200">
        <!-- Hero Section avec image de fond -->
        <div class="hero min-h-[400px] relative" style="background-image: url('https://picsum.photos/1920/400?random={{ $cinema->id }}');">
            <div class="hero-overlay bg-opacity-70"></div>
            <div class="hero-content text-center text-neutral-content relative z-10">
                <div class="max-w-4xl">
                    <h1 class="mb-5 text-5xl md:text-6xl font-bold">{{ $cinema->nom }}</h1>
                    @if($cinema->description)
                    <p class="mb-5 text-lg md:text-xl opacity-90">{{ $cinema->description }}</p>
                    @endif
                    <div class="flex flex-wrap justify-center gap-4">
                        <div class="badge badge-primary badge-lg">{{ $cinema->salles->count() }} salles</div>
                        <div class="badge badge-secondary badge-lg">{{ $seancesParFilm->count() }} films à l'affiche</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Colonne principale - Séances -->
                <div class="lg:col-span-2 space-y-6">
                    <h2 class="text-3xl font-bold mb-6">Séances à venir</h2>
                    
                    @forelse($seancesParFilm as $filmData)
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <div class="flex gap-4">
                                <!-- Affiche du film -->
                                <div class="w-24 md:w-32 flex-shrink-0">
                                    <img src="{{ $filmData['film']['affiche'] }}" 
                                         alt="{{ $filmData['film']['titre'] }}" 
                                         class="w-full rounded-lg shadow-md">
                                </div>
                                
                                <!-- Infos du film -->
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold mb-2">{{ $filmData['film']['titre'] }}</h3>
                                    <div class="flex flex-wrap gap-2 mb-4 text-sm">
                                        <span class="badge badge-outline">{{ $filmData['film']['duree'] }}</span>
                                        <span class="badge badge-outline">{{ $filmData['film']['genre'] }}</span>
                                        @if($filmData['film']['classification'])
                                        <span class="badge badge-warning badge-outline">{{ $filmData['film']['classification'] }}</span>
                                        @endif
                                        @if($filmData['film']['note_moyenne'])
                                        <span class="badge badge-accent badge-outline">
                                            ⭐ {{ $filmData['film']['note_moyenne'] }}/5
                                        </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Séances par jour -->
                                    <div class="space-y-3">
                                        @foreach($filmData['seances_par_jour'] as $jour)
                                        <div>
                                            <h4 class="font-semibold text-sm uppercase tracking-wider text-base-content/70 mb-2">
                                                {{ $jour['date_formatted'] }}
                                            </h4>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($jour['seances'] as $seance)
                                                @if($seance['places_disponibles'] > 0)
                                                <a href="{{ route('reservation.select-seats', $seance['sqid']) }}" 
                                                   class="btn btn-sm {{ $seance['places_disponibles'] < 10 ? 'btn-warning' : 'btn-outline' }}">
                                                    <span class="font-bold">{{ $seance['heure'] }}</span>
                                                    <span class="text-xs opacity-70">{{ $seance['salle'] }}</span>
                                                    @if($seance['version'] === 'VOSTFR')
                                                    <span class="badge badge-xs badge-info">VO</span>
                                                    @endif
                                                    @if($seance['qualite'] !== 'standard')
                                                    <span class="badge badge-xs badge-primary">{{ strtoupper($seance['qualite']) }}</span>
                                                    @endif
                                                    @if($seance['places_disponibles'] < 10)
                                                    <span class="text-xs text-warning">{{ $seance['places_disponibles'] }} pl.</span>
                                                    @endif
                                                </a>
                                                @else
                                                <button class="btn btn-sm btn-disabled" disabled>
                                                    <span class="font-bold">{{ $seance['heure'] }}</span>
                                                    <span class="text-xs opacity-70">{{ $seance['salle'] }}</span>
                                                    @if($seance['version'] === 'VOSTFR')
                                                    <span class="badge badge-xs badge-info">VO</span>
                                                    @endif
                                                    @if($seance['qualite'] !== 'standard')
                                                    <span class="badge badge-xs badge-primary">{{ strtoupper($seance['qualite']) }}</span>
                                                    @endif
                                                    <span class="text-xs text-error">Complet</span>
                                                </button>
                                                @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="card bg-base-100">
                        <div class="card-body text-center">
                            <p class="text-lg text-base-content/70">Aucune séance programmée à venir</p>
                        </div>
                    </div>
                    @endforelse
                    
                    <!-- Section Prochainement -->
                    @if($filmsProchainement->isNotEmpty())
                    <div class="mt-12">
                        <h2 class="text-3xl font-bold mb-6">Prochainement</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($filmsProchainement as $film)
                            <div class="card bg-base-100 shadow-lg">
                                <div class="card-body">
                                    <div class="flex gap-4">
                                        <div class="w-16 flex-shrink-0">
                                            <img src="{{ $film['affiche'] }}" 
                                                 alt="{{ $film['titre'] }}" 
                                                 class="w-full rounded-lg shadow-md">
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-bold mb-2">{{ $film['titre'] }}</h3>
                                            <div class="flex flex-wrap gap-2 text-sm">
                                                <span class="badge badge-outline">{{ $film['duree'] }}</span>
                                                <span class="badge badge-outline">{{ $film['genre'] }}</span>
                                                @if($film['note_moyenne'])
                                                <span class="badge badge-accent badge-outline">
                                                    ⭐ {{ $film['note_moyenne'] }}/5
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Colonne latérale - Informations pratiques -->
                <div class="space-y-6">
                    <!-- Carte de localisation -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title mb-4">Localisation</h3>
                            
                            <!-- Map placeholder -->
                            <div class="w-full h-64 bg-base-300 rounded-lg mb-4 relative overflow-hidden">
                                <img src="https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/{{ $coordonneesGPS['longitude'] }},{{ $coordonneesGPS['latitude'] }},15,0/400x300?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw" 
                                     alt="Carte" 
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-primary-content" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <p class="text-sm mb-4">{{ $informationsPratiques['contact']['adresse'] }}</p>
                            
                            <button class="btn btn-primary btn-block"
                                    onclick="window.open('https://maps.google.com?q={{ $coordonneesGPS['latitude'] }},{{ $coordonneesGPS['longitude'] }}', '_blank')">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Obtenir l'itinéraire
                            </button>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title mb-4">Contact</h3>
                            <div class="space-y-3">
                                <a href="tel:{{ $informationsPratiques['contact']['telephone'] }}" 
                                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-base-200 transition-colors">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span class="font-semibold">{{ $informationsPratiques['contact']['telephone'] }}</span>
                                </a>
                                
                                <a href="mailto:{{ $informationsPratiques['contact']['email'] }}" 
                                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-base-200 transition-colors">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $informationsPratiques['contact']['email'] }}</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Horaires -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title mb-4">Horaires d'ouverture</h3>
                            <div class="space-y-2">
                                @foreach($informationsPratiques['horaires'] as $horaire)
                                <div class="flex justify-between items-center py-2 {{ $horaire['is_today'] ? 'font-bold text-primary' : '' }}">
                                    <span>{{ $horaire['jour'] }}</span>
                                    <span>{{ $horaire['horaire'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title mb-4">Services disponibles</h3>
                            <div class="space-y-3">
                                @foreach($informationsPratiques['services'] as $service)
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $service['icon'] }}"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold">{{ $service['nom'] }}</h4>
                                        <p class="text-sm text-base-content/70">{{ $service['description'] }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app.layout>