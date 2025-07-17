<x-app.layout title="Mon Compte">
    <div class="min-h-screen bg-base-200">
        <!-- Hero Section -->
        <div class="hero bg-primary text-primary-content">
            <div class="hero-content text-center py-16">
                <div class="max-w-md">
                    <h1 class="text-5xl font-bold">Mon Compte</h1>
                    <p class="py-6">Gérez vos informations personnelles et vos préférences</p>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                @if(!$viewModel->estUtilisateurConnecte())
                    <!-- Message pour utilisateur non connecté -->
                    <div class="alert alert-warning mb-8">
                        <x-lucide-info class="w-5 h-5" />
                        <div>
                            <h3 class="font-bold">Vous n'êtes pas connecté</h3>
                            <div class="text-sm">Cette page affiche des données fictives. Connectez-vous pour voir vos vraies informations.</div>
                        </div>
                        <div>
                            <a href="{{ route('connexion') }}" class="btn btn-sm btn-primary">Se connecter</a>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Informations Personnelles -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title text-2xl mb-6">
                                <x-lucide-user class="w-6 h-6 mr-2" />
                                Informations Personnelles
                            </h2>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="label">
                                        <span class="label-text font-semibold">Email</span>
                                    </label>
                                    <div class="text-lg">{{ $viewModel->email() }}</div>
                                </div>
                                
                                <div>
                                    <label class="label">
                                        <span class="label-text font-semibold">Nom</span>
                                    </label>
                                    <div class="text-lg">{{ $viewModel->nom() }}</div>
                                </div>
                                
                                <div>
                                    <label class="label">
                                        <span class="label-text font-semibold">Membre depuis</span>
                                    </label>
                                    <div class="text-lg">{{ $viewModel->dateInscription() }}</div>
                                </div>
                                
                                <div>
                                    <label class="label">
                                        <span class="label-text font-semibold">Statut</span>
                                    </label>
                                    <div class="badge {{ $viewModel->classeBadgeStatut() }} text-lg p-3">
                                        {{ $viewModel->libelleBadgeStatut() }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-actions justify-end mt-6">
                                <button class="btn btn-primary" @if(!$viewModel->peutModifierProfil()) disabled @endif>
                                    <x-lucide-edit class="w-4 h-4 mr-2" />
                                    @if($viewModel->peutModifierProfil()) Modifier @else Modifier (Bientôt) @endif
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title text-2xl mb-6">
                                <x-lucide-bar-chart class="w-6 h-6 mr-2" />
                                Mes Statistiques
                            </h2>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="stat bg-primary text-primary-content rounded-lg">
                                    <div class="stat-title text-primary-content/70">Réservations</div>
                                    <div class="stat-value text-2xl">{{ $viewModel->nombreReservations() }}</div>
                                    <div class="stat-desc text-primary-content/70">au total</div>
                                </div>
                                
                                <div class="stat bg-secondary text-secondary-content rounded-lg">
                                    <div class="stat-title text-secondary-content/70">Films vus</div>
                                    <div class="stat-value text-2xl">{{ $viewModel->nombreFilmsVus() }}</div>
                                    <div class="stat-desc text-secondary-content/70">cette année</div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-4">
                                <x-lucide-info class="w-5 h-5" />
                                <span>{{ $viewModel->messageStatistiques() }}</span>
                            </div>
                            
                            <div class="card-actions justify-end mt-6">
                                <a href="{{ $viewModel->lienMesReservations() }}" class="btn btn-outline">
                                    <x-lucide-calendar class="w-4 h-4 mr-2" />
                                    Voir mes réservations
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Rapides -->
                <div class="card bg-base-100 shadow-xl mt-8">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-6">
                            <x-lucide-zap class="w-6 h-6 mr-2" />
                            Actions Rapides
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="{{ $viewModel->lienFilms() }}" class="btn btn-outline btn-lg">
                                <x-lucide-film class="w-5 h-5 mr-2" />
                                Voir les films
                            </a>
                            
                            <a href="{{ $viewModel->lienCinemas() }}" class="btn btn-outline btn-lg">
                                <x-lucide-map-pin class="w-5 h-5 mr-2" />
                                Nos cinémas
                            </a>
                            
                            <a href="{{ $viewModel->lienMesReservations() }}" class="btn btn-outline btn-lg">
                                <x-lucide-ticket class="w-5 h-5 mr-2" />
                                Mes billets
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app.layout>