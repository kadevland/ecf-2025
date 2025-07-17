<x-app.layout title="Confirmation de réservation">
    <!-- Hero -->
    <section class="bg-primary text-primary-content py-8">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold">Confirmation de votre réservation</h1>
            <p class="text-primary-content/80 mt-2">Vérifiez vos informations et finalisez votre réservation</p>
        </div>
    </section>

    <!-- Contenu principal -->
    <section class="py-12">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Récapitulatif de la séance -->
                <div class="bg-base-200 rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-6">Récapitulatif</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm text-base-content/70">Film:</span>
                            <div class="font-semibold text-lg">{{ $reservation['seance']['film_titre'] }}</div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-base-content/70">Cinéma:</span>
                                <div class="font-medium">{{ $reservation['seance']['cinema_nom'] }}</div>
                            </div>
                            <div>
                                <span class="text-sm text-base-content/70">Salle:</span>
                                <div class="font-medium">{{ $reservation['seance']['salle_nom'] }}</div>
                            </div>
                        </div>
                        
                        <div>
                            <span class="text-sm text-base-content/70">Date et heure:</span>
                            <div class="font-medium">{{ $reservation['seance']['date_heure'] }}</div>
                        </div>
                        
                        <div>
                            <span class="text-sm text-base-content/70">Version:</span>
                            <div class="font-medium">{{ $reservation['seance']['version'] }}</div>
                        </div>
                        
                        <hr class="border-base-300">
                        
                        <div>
                            <span class="text-sm text-base-content/70">Places sélectionnées:</span>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($reservation['places'] as $place)
                                <span class="badge badge-primary">{{ $place }}</span>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-base-content/70">Nombre de places:</span>
                                <div class="font-semibold">{{ $reservation['nombre_places'] }}</div>
                            </div>
                            <div>
                                <span class="text-sm text-base-content/70">Prix par place:</span>
                                <div class="font-semibold">{{ number_format($reservation['prix_unitaire'], 2, ',', ' ') }} €</div>
                            </div>
                        </div>
                        
                        <hr class="border-base-300">
                        
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold">Total:</span>
                            <span class="text-2xl font-bold text-primary">{{ number_format($reservation['prix_total'], 2, ',', ' ') }} €</span>
                        </div>
                    </div>
                </div>
                
                <!-- Authentification et informations -->
                <div class="bg-white rounded-lg p-6 shadow-lg border">
                    @guest
                        <!-- Utilisateur non connecté -->
                        <h2 class="text-2xl font-bold mb-6">Authentification requise</h2>
                        <p class="text-base-content/70 mb-6">Pour finaliser votre réservation, vous devez vous connecter ou créer un compte.</p>
                        
                        <div class="space-y-4">
                            <a href="{{ route('connexion') }}" class="btn btn-primary w-full">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                J'ai un compte - Me connecter
                            </a>
                            
                            <a href="{{ route('creer-compte') }}" class="btn btn-outline w-full">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Créer un compte
                            </a>
                        </div>
                    @else
                        <!-- Utilisateur connecté -->
                        <h2 class="text-2xl font-bold mb-6">Finalisation de la réservation</h2>
                        
                        <!-- Informations utilisateur -->
                        <div class="bg-primary/10 rounded-lg p-4 mb-6">
                            <h3 class="font-semibold text-primary mb-2">Connecté en tant que</h3>
                            <p class="text-lg"><strong>{{ auth()->user()->name }}</strong></p>
                            <p class="text-base-content/70">{{ auth()->user()->email }}</p>
                        </div>
                        
                        @php
                            $mesReservations = \App\Models\Reservation::where('user_id', auth()->id())
                                ->where('seance_id', $reservation['seance']['id'])
                                ->with('billets')
                                ->get();
                        @endphp
                        
                        @if($mesReservations->isNotEmpty())
                            <!-- Mes réservations pour cette séance -->
                            <div class="bg-warning/10 rounded-lg p-4 mb-6">
                                <h3 class="font-semibold text-warning mb-3">Mes réservations pour cette séance</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($mesReservations as $resa)
                                        @foreach($resa->billets as $billet)
                                            <span class="badge badge-warning">{{ $billet->place }}</span>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- Formulaire de finalisation -->
                        <form action="{{ route('reservation.finalize') }}" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" name="seance_id" value="{{ $reservation['seance']['id'] }}">
                            @foreach($reservation['places'] as $place)
                                <input type="hidden" name="seats[]" value="{{ $place }}">
                            @endforeach
                            
                            <!-- Informations automatiques -->
                            <input type="hidden" name="client_nom" value="{{ explode(' ', auth()->user()->name)[0] ?? '' }}">
                            <input type="hidden" name="client_prenom" value="{{ explode(' ', auth()->user()->name)[1] ?? explode(' ', auth()->user()->name)[0] }}">
                            <input type="hidden" name="client_email" value="{{ auth()->user()->email }}">
                            <input type="hidden" name="client_telephone" value="{{ auth()->user()->phone ?? '0000000000' }}">
                            
                            <!-- CGV -->
                            <div class="form-control">
                                <label class="cursor-pointer label justify-start gap-3">
                                    <input type="checkbox" class="checkbox checkbox-primary" required>
                                    <span class="label-text">
                                        J'accepte les 
                                        <a href="#" class="link link-primary">conditions générales de vente</a>
                                        et la 
                                        <a href="#" class="link link-primary">politique de confidentialité</a>
                                    </span>
                                </label>
                            </div>
                            
                            <!-- Boutons d'action -->
                            <div class="flex flex-col sm:flex-row gap-4 pt-6">
                                <a href="{{ url()->previous() }}" class="btn btn-outline flex-1">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    Modifier les places
                                </a>
                                <button type="submit" class="btn btn-primary flex-1">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Finaliser la réservation
                                </button>
                            </div>
                        </form>
                    @endguest
                </div>
            </div>
            
            <!-- Informations complémentaires -->
            <div class="mt-12 bg-info/10 rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4 text-info">Informations importantes</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <h4 class="font-semibold mb-2">Modalités de réservation</h4>
                        <ul class="space-y-1 text-base-content/70">
                            <li>• Arrivée recommandée 15 minutes avant la séance</li>
                            <li>• Présentation d'une pièce d'identité requise</li>
                            <li>• Les places non occupées 10 minutes après le début peuvent être revendues</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-2">Annulation</h4>
                        <ul class="space-y-1 text-base-content/70">
                            <li>• Annulation gratuite jusqu'à 2h avant la séance</li>
                            <li>• Contact: <a href="tel:0123456789" class="link">01 23 45 67 89</a></li>
                            <li>• Ou par email: <a href="mailto:contact@cinephoria.fr" class="link">contact@cinephoria.fr</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app.layout>