<x-app.layout title="Accueil">
    <!-- Hero Section -->
    <div class="hero min-h-[80vh] bg-gradient-to-r from-primary via-primary-focus to-secondary relative overflow-hidden">
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute transform rotate-12 translate-x-1/4 translate-y-1/4">
                <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18 3v2h-2V3H8v2H6V3H4v18h2v-2h2v2h8v-2h2v2h2V3h-2zM8 17H6v-2h2v2zm0-4H6v-2h2v2zm0-4H6V7h2v2zm10 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V7h2v2z"/>
                </svg>
            </div>
            <div class="absolute transform -rotate-12 translate-x-3/4 translate-y-1/2">
                <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24">
                    <path d="m18.378 16.406-1.484-3.883c-1.071.052-2.205.085-3.394.085s-2.323-.033-3.394-.085l-1.484 3.883a1.001 1.001 0 01-1.847-.706l1.342-3.509C6.42 11.732 5.828 11.249 5.828 10.659V7.5c0-1.038 1.203-1.875 2.687-1.875h6.97c1.484 0 2.687.837 2.687 1.875v3.159c0 .59-.592 1.073-1.269 1.532l1.342 3.509a1.001 1.001 0 01-1.847.706z"/>
                </svg>
            </div>
        </div>
        
        <div class="hero-content text-center text-neutral-content relative z-10">
            <div class="max-w-4xl">
                <h1 class="mb-6 text-5xl md:text-7xl font-bold">Bienvenue chez <span class="text-accent">Cinéphoria</span></h1>
                <p class="mb-8 text-xl md:text-2xl opacity-90">L'expérience cinéma ultime en France et en Belgique</p>
                
                <!-- Statistiques en hero -->
                <div class="flex justify-center gap-8 mb-8">
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-accent">{{ $statistiques['films'] }}</div>
                        <div class="text-sm md:text-base text-primary-content/70 uppercase tracking-wider">Films</div>
                    </div>
                    <div class="w-px h-12 bg-primary-content/20"></div>
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-accent">{{ $statistiques['cinemas'] }}</div>
                        <div class="text-sm md:text-base text-primary-content/70 uppercase tracking-wider">Cinémas</div>
                    </div>
                    <div class="w-px h-12 bg-primary-content/20"></div>
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-accent">{{ $statistiques['salles'] }}</div>
                        <div class="text-sm md:text-base text-primary-content/70 uppercase tracking-wider">Salles</div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('films.index') }}" class="btn btn-accent btn-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2C7 1.45 7.45 1 8 1h8c.55 0 1 .45 1 1v2h5c.55 0 1 .45 1 1s-.45 1-1 1h-1v14c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V6H2c-.55 0-1-.45-1-1s.45-1 1-1h5z"/>
                        </svg>
                        Voir les films
                    </a>
                    <a href="{{ route('cinemas.index') }}" class="btn btn-outline btn-accent btn-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        Nos cinémas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Films à l'affiche -->
    <section class="py-16 bg-base-200">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4">Films à l'affiche</h2>
                <p class="text-lg text-base-content/70">Découvrez les dernières sorties dans nos cinémas</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($filmsALAffiche as $film)
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                    <figure class="relative">
                        <img src="{{ $film['affiche'] }}" alt="{{ $film['titre'] }}" class="w-full h-96 object-cover" />
                        @if($film['note'])
                        <div class="absolute top-2 right-2 badge badge-primary">
                            <svg class="w-4 h-4 mr-1 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            {{ number_format($film['note'], 1) }}
                        </div>
                        @endif
                    </figure>
                    <div class="card-body">
                        <h3 class="card-title text-lg">{{ $film['titre'] }}</h3>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="badge badge-outline badge-sm">{{ $film['categorie'] }}</span>
                            <span class="badge badge-outline badge-sm">{{ $film['duree'] }}</span>
                        </div>
                        <div class="card-actions justify-between items-center">
                            <div class="text-sm text-base-content/60">
                                {{ $film['note_formatee'] }}
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('films.show', $film['model']) }}" class="btn btn-ghost btn-sm">
                                    Détails
                                </a>
                                <a href="{{ route('films.show', $film['model']) }}" class="btn btn-primary btn-sm">
                                    Réserver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('films.index') }}" class="btn btn-outline btn-primary btn-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2C7 1.45 7.45 1 8 1h8c.55 0 1 .45 1 1v2h5c.55 0 1 .45 1 1s-.45 1-1 1h-1v14c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V6H2c-.55 0-1-.45-1-1s.45-1 1-1h5z"/>
                    </svg>
                    Voir tous les films ({{ $statistiques['films'] }})
                </a>
            </div>
        </div>
    </section>

    <!-- Prochaines séances -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4">Prochaines séances</h2>
                <p class="text-lg text-base-content/70">Réservez vos places pour aujourd'hui et demain</p>
            </div>
            
            @if($prochainesSeances->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($prochainesSeances as $seance)
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow border border-base-300">
                    <div class="card-body">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="card-title text-lg">{{ $seance['film'] }}</h3>
                            <div class="badge badge-primary">{{ $seance['date'] }}</div>
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Cinéma:</span>
                                <span class="font-medium">{{ $seance['cinema'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Heure:</span>
                                <span class="font-bold text-primary">{{ $seance['heure'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Salle:</span>
                                <span>{{ $seance['salle'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Prix:</span>
                                <span class="font-semibold">{{ $seance['prix'] }}</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-2 mt-3">
                            @if($seance['version'] === 'VOSTFR')
                            <span class="badge badge-info badge-sm">VO</span>
                            @endif
                            @if($seance['qualite'] !== 'Standard')
                            <span class="badge badge-secondary badge-sm">{{ $seance['qualite'] }}</span>
                            @endif
                            @if($seance['places_disponibles'] < 10)
                            <span class="badge badge-warning badge-sm">{{ $seance['places_disponibles'] }} places</span>
                            @endif
                        </div>
                        
                        <div class="card-actions justify-between items-center mt-4">
                            <a href="{{ route('films.show', $seance['film_model']) }}" class="btn btn-ghost btn-sm">
                                Voir le film
                            </a>
                            <a href="{{ route('films.show', $seance['film_model']) }}" class="btn btn-primary btn-sm">
                                Réserver
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <svg class="w-24 h-24 mx-auto text-base-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-xl font-semibold text-base-content/70 mb-2">Aucune séance programmée</h3>
                <p class="text-base-content/50 mb-4">Consultez notre programmation complète</p>
                <a href="{{ route('films.index') }}" class="btn btn-primary">Voir tous les films</a>
            </div>
            @endif
        </div>
    </section>

    <!-- Nos cinémas -->
    <section class="py-16 bg-base-200">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4">Nos cinémas</h2>
                <p class="text-lg text-base-content/70">Découvrez nos établissements en France et en Belgique</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                @foreach($cinemasPopulaires as $cinema)
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                    <figure>
                        <img src="{{ $cinema['photo'] }}" alt="{{ $cinema['nom'] }}" class="w-full h-48 object-cover" />
                    </figure>
                    <div class="card-body">
                        <h3 class="card-title">{{ $cinema['nom'] }}</h3>
                        <p class="text-base-content/70">{{ $cinema['ville'] }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="text-sm">{{ $cinema['nombre_salles'] }} salles</span>
                        </div>
                        <div class="card-actions justify-end mt-4">
                            <a href="{{ route('cinemas.show', $cinema['model']) }}" class="btn btn-primary btn-sm">
                                Voir les séances
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="text-center">
                <a href="{{ route('cinemas.index') }}" class="btn btn-outline btn-primary btn-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    Tous nos cinémas ({{ $statistiques['cinemas'] }})
                </a>
            </div>
        </div>
    </section>

    <!-- Nos avantages -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4">Pourquoi choisir Cinéphoria ?</h2>
                <p class="text-lg text-base-content/70">L'expérience cinéma ultime vous attend</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card bg-base-100 shadow-xl border border-primary/20">
                    <div class="card-body text-center">
                        <div class="w-20 h-20 mx-auto mb-4 text-primary bg-primary/10 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                            </svg>
                        </div>
                        <h3 class="card-title justify-center text-xl mb-3">Technologies de pointe</h3>
                        <p class="text-base-content/70">IMAX, Dolby Atmos, écrans LED dernière génération pour une immersion totale</p>
                    </div>
                </div>
                
                <div class="card bg-base-100 shadow-xl border border-secondary/20">
                    <div class="card-body text-center">
                        <div class="w-20 h-20 mx-auto mb-4 text-secondary bg-secondary/10 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="card-title justify-center text-xl mb-3">{{ $statistiques['cinemas'] }} cinémas</h3>
                        <p class="text-base-content/70">En France et en Belgique, avec {{ $statistiques['salles'] }} salles au total, toujours près de chez vous</p>
                    </div>
                </div>
                
                <div class="card bg-base-100 shadow-xl border border-accent/20">
                    <div class="card-body text-center">
                        <div class="w-20 h-20 mx-auto mb-4 text-accent bg-accent/10 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="card-title justify-center text-xl mb-3">Tarifs attractifs</h3>
                        <p class="text-base-content/70">Cartes d'abonnement, réductions étudiants et offres famille disponibles</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="py-16 bg-gradient-to-r from-primary to-secondary text-primary-content relative overflow-hidden">
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute transform rotate-45 translate-x-1/2 translate-y-1/2">
                <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">Restez informé</h2>
                <p class="text-xl mb-8 opacity-90">Inscrivez-vous à notre newsletter pour recevoir nos actualités, avant-premières et offres exclusives</p>
                
                <form class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto mb-6">
                    <input type="email" 
                           placeholder="Votre adresse email" 
                           class="input input-bordered input-lg flex-1 text-base-content" 
                           required />
                    <button type="submit" class="btn btn-accent btn-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        S'inscrire
                    </button>
                </form>
                
                <p class="text-sm opacity-70">🎬 {{ $statistiques['seances_semaine'] }} séances cette semaine dans nos {{ $statistiques['cinemas'] }} cinémas</p>
            </div>
        </div>
    </section>
</x-app.layout>