<x-app.layout title="Réservation confirmée">
    <!-- Hero de succès -->
    <section class="bg-success text-success-content py-16">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-2xl mx-auto">
                <!-- Icône de succès -->
                <div class="w-24 h-24 mx-auto mb-6 bg-success-content/20 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-success-content" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Réservation confirmée !</h1>
                <p class="text-xl text-success-content/90 mb-6">
                    Votre réservation a été enregistrée avec succès.
                </p>
                <p class="text-lg text-success-content/80">
                    Un email de confirmation a été envoyé à <strong>{{ $reservation['client']['email'] }}</strong>
                </p>
            </div>
        </div>
    </section>

    <!-- Détails de la réservation -->
    <section class="py-12">
        <div class="container mx-auto px-4 max-w-4xl">
            
            <!-- Numéro de réservation -->
            <div class="text-center mb-12">
                <div class="inline-block bg-primary text-primary-content px-8 py-4 rounded-lg">
                    <div class="text-sm uppercase tracking-wider opacity-80 mb-1">Numéro de réservation</div>
                    <div class="text-2xl font-bold font-mono">{{ $reservation['numero'] }}</div>
                </div>
                <p class="text-sm text-base-content/70 mt-3">
                    Conservez ce numéro, il vous sera demandé à l'accueil du cinéma
                </p>
            </div>

            <!-- Billet de cinéma stylisé -->
            <div class="bg-white rounded-lg shadow-2xl overflow-hidden mb-8 border">
                <div class="bg-gradient-to-r from-primary to-secondary text-white p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h2 class="text-2xl font-bold">{{ $reservation['seance']['film_titre'] }}</h2>
                            <p class="text-white/80">{{ $reservation['seance']['version'] }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm opacity-80">{{ $reservation['date_reservation'] }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Style de découpe de billet -->
                <div class="relative">
                    <div class="absolute left-0 top-0 w-8 h-8 bg-base-100 rounded-full transform -translate-x-4 -translate-y-4"></div>
                    <div class="absolute right-0 top-0 w-8 h-8 bg-base-100 rounded-full transform translate-x-4 -translate-y-4"></div>
                    <div class="border-t-2 border-dashed border-base-300"></div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Informations séance -->
                        <div>
                            <h3 class="font-bold text-lg mb-4 text-primary">Informations de la séance</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-base-content/70">Cinéma:</span>
                                    <span class="font-medium">{{ $reservation['seance']['cinema_nom'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-base-content/70">Adresse:</span>
                                    <span class="font-medium text-right">{{ $reservation['seance']['cinema_adresse'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-base-content/70">Salle:</span>
                                    <span class="font-medium">{{ $reservation['seance']['salle_nom'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-base-content/70">Date et heure:</span>
                                    <span class="font-bold text-primary">{{ $reservation['seance']['date_heure'] }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informations client et places -->
                        <div>
                            <h3 class="font-bold text-lg mb-4 text-primary">Votre réservation</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-base-content/70">Client:</span>
                                    <span class="font-medium">{{ $reservation['client']['prenom'] }} {{ $reservation['client']['nom'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-base-content/70">Téléphone:</span>
                                    <span class="font-medium">{{ $reservation['client']['telephone'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-base-content/70">Places:</span>
                                    <div class="flex flex-wrap gap-1 justify-end">
                                        @foreach($reservation['places'] as $place)
                                        <span class="badge badge-primary badge-sm">{{ $place }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t">
                                    <span class="font-semibold">Total payé:</span>
                                    <span class="text-xl font-bold text-primary">{{ number_format($reservation['prix_total'], 2, ',', ' ') }} €</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions importantes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="card bg-warning/10 border border-warning/20">
                    <div class="card-body">
                        <h3 class="card-title text-warning">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            À votre arrivée
                        </h3>
                        <ul class="text-sm space-y-2">
                            <li>• Présentez votre <strong>numéro de réservation</strong> ou cet email</li>
                            <li>• Une <strong>pièce d'identité</strong> vous sera demandée</li>
                            <li>• Arrivez <strong>15 minutes avant</strong> le début de la séance</li>
                            <li>• Récupérez vos billets aux <strong>bornes automatiques</strong> ou à l'accueil</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card bg-info/10 border border-info/20">
                    <div class="card-body">
                        <h3 class="card-title text-info">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Bon à savoir
                        </h3>
                        <ul class="text-sm space-y-2">
                            <li>• Annulation <strong>gratuite</strong> jusqu'à 2h avant la séance</li>
                            <li>• Places <strong>garanties</strong> jusqu'à 10 minutes après le début</li>
                            <li>• En cas de problème: <strong>01 23 45 67 89</strong></li>
                            <li>• Email: <strong>contact@cinephoria.fr</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="text-center space-y-4">
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="window.print()" class="btn btn-outline btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Imprimer
                    </button>
                    
                    <a href="mailto:?subject=Réservation%20Cinéphoria&body=Ma%20réservation%20{{ $reservation['numero'] }}%20pour%20{{ $reservation['seance']['film_titre'] }}%20le%20{{ $reservation['seance']['date_heure'] }}" 
                       class="btn btn-outline">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                        </svg>
                        Partager
                    </a>
                    
                    <a href="{{ route('accueil') }}" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Retour à l'accueil
                    </a>
                </div>
                
                <p class="text-sm text-base-content/60">
                    Merci de votre confiance et excellente séance ! 🍿
                </p>
            </div>
        </div>
    </section>

    <!-- Style d'impression -->
    <style>
        @media print {
            .navbar, .footer, .btn {
                display: none !important;
            }
            
            .container {
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            body {
                background: white !important;
                color: black !important;
            }
        }
    </style>
</x-app.layout>