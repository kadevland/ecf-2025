<x-app.layout :title="$film['titre']">
    <div class="min-h-screen bg-base-200">
        <!-- Hero Section avec image de fond du film -->
        <div class="hero min-h-[500px] relative" style="background-image: url('{{ $film['affiche'] }}');">
            <div class="hero-overlay bg-opacity-80"></div>
            <div class="hero-content text-center text-neutral-content relative z-10">
                <div class="max-w-6xl flex flex-col lg:flex-row gap-8 items-center">
                    <!-- Affiche du film -->
                    <div class="w-64 flex-shrink-0">
                        <img src="{{ $film['affiche'] }}" alt="{{ $film['titre'] }}" class="w-full rounded-lg shadow-2xl">
                    </div>

                    <!-- Informations du film -->
                    <div class="text-left flex-1">
                        <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $film['titre'] }}</h1>

                        <!-- Badges informatifs -->
                        <div class="flex flex-wrap gap-3 mb-6">
                            <div class="badge badge-primary badge-lg">{{ $film['categorie'] }}</div>
                            <div class="badge badge-secondary badge-lg">{{ $film['duree'] }}</div>
                            @if ($film['date_sortie_formatee'])
                                <div class="badge badge-accent badge-lg">{{ $film['date_sortie_formatee'] }}</div>
                            @endif
                            @if ($film['note_moyenne'])
                                <div class="badge badge-warning badge-lg">
                                    ⭐ {{ number_format($film['note_moyenne'], 1) }}/10
                                    @if ($film['nombre_votes'] > 0)
                                        <span class="text-xs ml-1">({{ $film['nombre_votes'] }} votes)</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if ($film['realisateur'])
                            <p class="text-lg mb-2 opacity-90">
                                <span class="font-semibold">Réalisé par :</span> {{ $film['realisateur'] }}
                            </p>
                        @endif

                        @if ($film['pays_origine'])
                            <p class="text-lg mb-4 opacity-90">
                                <span class="font-semibold">Pays :</span> {{ $film['pays_origine'] }}
                            </p>
                        @endif

                        @if ($film['description'])
                            <p class="text-lg opacity-90 leading-relaxed max-w-3xl">{{ $film['description'] }}</p>
                        @endif

                        @if ($film['acteurs'] && count($film['acteurs']) > 0)
                            <div class="mt-4">
                                <p class="text-lg font-semibold mb-2">Avec :</p>
                                <p class="text-base opacity-80">
                                    {{ implode(', ', array_slice($film['acteurs'], 0, 5)) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Colonne principale - Séances -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-3xl font-bold">Séances dans nos cinémas</h2>
                        @if ($film['has_trailer'])
                            <a href="{{ $film['bande_annonce'] }}" target="_blank" class="btn btn-outline btn-primary">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1m6-6a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Voir la bande-annonce
                            </a>
                        @endif
                    </div>

                    @forelse($seancesParCinema as $cinemaData)
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold text-primary">{{ $cinemaData['cinema']['nom'] }}
                                        </h3>
                                        <p class="text-sm text-base-content/70">
                                            {{ $cinemaData['cinema']['adresse_complete'] }}</p>
                                    </div>
                                    <a href="{{ route('cinemas.show', \App\Models\Cinema::find($cinemaData['cinema']['id'])) }}"
                                        class="btn btn-ghost btn-sm">
                                        Voir le cinéma
                                    </a>
                                </div>

                                <!-- Séances par jour -->
                                <div class="space-y-4">
                                    @foreach ($cinemaData['seances_par_jour'] as $jour)
                                        <div>
                                            <h4
                                                class="font-semibold text-base uppercase tracking-wider text-base-content/70 mb-3 flex items-center gap-2">
                                                {{ $jour['date_formatted'] }}
                                                @if ($jour['is_today'])
                                                    <span class="badge badge-primary badge-sm">Aujourd'hui</span>
                                                @elseif($jour['is_tomorrow'])
                                                    <span class="badge badge-secondary badge-sm">Demain</span>
                                                @endif
                                            </h4>
                                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                                @foreach ($jour['seances'] as $seance)
                                                    @if ($seance['complet'])
                                                        <button class="btn btn-outline btn-disabled btn-sm">
                                                        @else
                                                            <a href="{{ route('reservation.select-seats', \App\Models\Seance::find($seance['id'])) }}"
                                                                class="btn btn-outline {{ $seance['peu_de_places'] ? 'btn-warning' : 'btn-primary' }} btn-sm">
                                                    @endif

                                                    <div class="font-bold">{{ $seance['heure'] }}</div>
                                                    <div class="text-xs opacity-70">
                                                        {{ $seance['salle'] }}
                                                        @if ($seance['version'] === 'VOSTFR')
                                                            • VO
                                                        @endif
                                                        @if ($seance['qualite'] !== 'standard')
                                                            • {{ strtoupper($seance['qualite']) }}
                                                        @endif
                                                    </div>
                                                    @if ($seance['complet'])
                                                        <div class="text-xs text-error">Complet</div>
                                                    @elseif($seance['peu_de_places'])
                                                        <div class="text-xs text-warning">
                                                            {{ $seance['places_disponibles'] }} places</div>
                                                    @else
                                                        <div class="text-xs">{{ $seance['prix'] }}</div>
                                                    @endif

                                                    @if ($seance['complet'])
                                                        </button>
                                                    @else
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card bg-base-100">
                            <div class="card-body text-center">
                                <svg class="w-16 h-16 mx-auto text-base-300 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-base-content/70 mb-2">Aucune séance programmée
                                </h3>
                                <p class="text-base-content/50">
                                    Ce film n'a pas de séances programmées dans les 14 prochains jours.
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Colonne latérale - Informations complémentaires -->
                <div class="space-y-6">
                    <!-- Statistiques rapides -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title mb-4">Informations</h3>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-base-content/70">Durée</span>
                                    <span class="font-semibold">{{ $film['duree'] }}</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-base-content/70">Genre</span>
                                    <span class="font-semibold">{{ $film['categorie'] }}</span>
                                </div>

                                @if ($film['pays_origine'])
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-base-content/70">Pays</span>
                                        <span class="font-semibold">{{ $film['pays_origine'] }}</span>
                                    </div>
                                @endif

                                @if ($film['date_sortie'])
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-base-content/70">Sortie</span>
                                        <span class="font-semibold">{{ $film['date_sortie_formatee'] }}</span>
                                    </div>
                                @endif

                                @if ($film['note_moyenne'])
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-base-content/70">Note</span>
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                            <span
                                                class="font-semibold">{{ number_format($film['note_moyenne'], 1) }}/10</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Séances disponibles -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title mb-4">Séances disponibles</h3>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-base-content/70">Cinémas</span>
                                    <span class="font-semibold">{{ $nombreCinemas }}</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-base-content/70">Séances</span>
                                    <span class="font-semibold">{{ $nombreSeances }}</span>
                                </div>
                            </div>

                            @if ($nombreSeances > 0)
                                <div class="mt-4">
                                    <div class="text-xs text-base-content/50 mb-2">Légende :</div>
                                    <div class="space-y-1 text-xs">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 bg-primary rounded"></div>
                                            <span>Places disponibles</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 bg-warning rounded"></div>
                                            <span>Peu de places</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 bg-base-300 rounded"></div>
                                            <span>Complet</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($film['from_tmdb'])
                        <!-- Source TMDB -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <div class="flex items-center gap-2 text-sm text-base-content/60">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Données provenant de The Movie Database</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app.layout>
