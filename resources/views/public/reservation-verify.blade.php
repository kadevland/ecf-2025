<!DOCTYPE html>
<html>
<head>
    <title>Vérification Réservation</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-base-200">
        <!-- Hero Section -->
        <div class="hero bg-primary text-primary-content">
            <div class="hero-content text-center py-12">
                <div class="max-w-md">
                    <div class="w-16 h-16 mx-auto mb-4 bg-primary-content rounded-full flex items-center justify-center">
                        <span class="text-primary text-2xl">✓</span>
                    </div>
                    <h1 class="text-4xl font-bold">Vérification Réservation</h1>
                    <p class="py-4">Validation de votre réservation</p>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-2xl mx-auto">
                <!-- Statut de la réservation -->
                <div class="alert {{ $reservation->statut === 'confirmee' || $reservation->statut === 'payee' ? 'alert-success' : ($reservation->statut === 'annulee' ? 'alert-error' : 'alert-warning') }} mb-6">
                    <span class="text-lg">ℹ️</span>
                    <span>
                        <strong>Statut :</strong> {{ $reservation->getStatutLabel() }}
                        @if($reservation->statut === 'confirmee' || $reservation->statut === 'payee')
                            - Réservation valide
                        @elseif($reservation->statut === 'annulee')
                            - Réservation annulée
                        @else
                            - Réservation en attente
                        @endif
                    </span>
                </div>

                <!-- Informations de la réservation -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="card-title text-2xl">Détails de la Réservation</h2>
                            <div class="badge badge-primary badge-lg">{{ $reservation->numero_reservation }}</div>
                        </div>

                        <!-- Informations du film -->
                        <div class="flex items-center gap-4 mb-6">
                            <div class="avatar">
                                <div class="w-20 h-20 rounded-lg">
                                    <img 
                                        src="{{ $reservation->seance->film->affiche_url ?? 'https://via.placeholder.com/80x80' }}" 
                                        alt="Affiche du film" 
                                        class="object-cover"
                                    >
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-primary">{{ $reservation->seance->film->titre }}</h3>
                                <p class="text-base-content/70">{{ $reservation->seance->film->categorie }}</p>
                                <p class="text-sm text-base-content/50">{{ $reservation->seance->film->duree_minutes }} minutes</p>
                            </div>
                        </div>

                        <!-- Informations de la séance -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-figure text-primary">
                                    <x-lucide-map-pin class="w-6 h-6" />
                                </div>
                                <div class="stat-title">Cinéma</div>
                                <div class="stat-value text-base">{{ $reservation->seance->salle->cinema->nom }}</div>
                                <div class="stat-desc">{{ $reservation->seance->salle->cinema->adresse }}</div>
                            </div>

                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-figure text-primary">
                                    <x-lucide-armchair class="w-6 h-6" />
                                </div>
                                <div class="stat-title">Salle</div>
                                <div class="stat-value text-base">{{ $reservation->seance->salle->nom }}</div>
                                <div class="stat-desc">{{ $reservation->seance->salle->type_salle }}</div>
                            </div>
                        </div>

                        <!-- Informations de la séance -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-figure text-primary">
                                    <x-lucide-calendar class="w-6 h-6" />
                                </div>
                                <div class="stat-title">Date</div>
                                <div class="stat-value text-sm">{{ $reservation->seance->date_heure_debut->format('d/m/Y') }}</div>
                                <div class="stat-desc">{{ $reservation->seance->date_heure_debut->format('l') }}</div>
                            </div>

                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-figure text-primary">
                                    <x-lucide-clock class="w-6 h-6" />
                                </div>
                                <div class="stat-title">Heure</div>
                                <div class="stat-value text-sm">{{ $reservation->seance->date_heure_debut->format('H\hi') }}</div>
                                <div class="stat-desc">Début séance</div>
                            </div>

                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-figure text-primary">
                                    <x-lucide-users class="w-6 h-6" />
                                </div>
                                <div class="stat-title">Places</div>
                                <div class="stat-value text-sm">{{ $reservation->nombre_places }}</div>
                                <div class="stat-desc">{{ $reservation->nombre_places > 1 ? 'billets' : 'billet' }}</div>
                            </div>
                        </div>

                        <!-- Informations techniques de la séance -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-figure text-primary">
                                    <x-lucide-monitor class="w-6 h-6" />
                                </div>
                                <div class="stat-title">Qualité</div>
                                <div class="stat-value text-sm">{{ strtoupper($reservation->seance->qualite_projection) }}</div>
                                <div class="stat-desc">Projection</div>
                            </div>

                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-figure text-primary">
                                    <x-lucide-languages class="w-6 h-6" />
                                </div>
                                <div class="stat-title">Version</div>
                                <div class="stat-value text-sm">{{ strtoupper($reservation->seance->version_linguistique) }}</div>
                                <div class="stat-desc">Langue</div>
                            </div>
                        </div>

                        <!-- Informations de prix -->
                        <div class="divider">Prix</div>
                        <div class="flex justify-between items-center text-lg">
                            <span>Prix total</span>
                            <span class="font-bold text-primary">{{ number_format($reservation->prix_total, 2) }} €</span>
                        </div>
                        @if($reservation->montant_paye)
                            <div class="flex justify-between items-center text-sm text-base-content/70">
                                <span>Montant payé</span>
                                <span>{{ number_format($reservation->montant_paye, 2) }} €</span>
                            </div>
                        @endif

                        <!-- Date de réservation -->
                        <div class="divider">Informations</div>
                        <div class="flex justify-between items-center text-sm text-base-content/70">
                            <span>Réservé le</span>
                            <span>{{ $reservation->created_at->format('d/m/Y à H\hi') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="card bg-base-100 shadow-xl mt-6">
                    <div class="card-body">
                        <h3 class="text-lg font-bold mb-4">
                            <x-lucide-info class="w-5 h-5 inline mr-2" />
                            Instructions
                        </h3>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-start">
                                <x-lucide-check class="w-4 h-4 text-success mr-2 mt-0.5 flex-shrink-0" />
                                <span>Présentez-vous à l'accueil du cinéma 15 minutes avant le début de la séance</span>
                            </li>
                            <li class="flex items-start">
                                <x-lucide-check class="w-4 h-4 text-success mr-2 mt-0.5 flex-shrink-0" />
                                <span>Ayez votre QR code ou votre numéro de réservation à portée de main</span>
                            </li>
                            <li class="flex items-start">
                                <x-lucide-check class="w-4 h-4 text-success mr-2 mt-0.5 flex-shrink-0" />
                                <span>Les places sont garanties jusqu'à 10 minutes avant le début de la séance</span>
                            </li>
                            @if($reservation->statut === 'en_attente')
                                <li class="flex items-start">
                                    <x-lucide-alert-triangle class="w-4 h-4 text-warning mr-2 mt-0.5 flex-shrink-0" />
                                    <span>Votre réservation est en attente de confirmation</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>