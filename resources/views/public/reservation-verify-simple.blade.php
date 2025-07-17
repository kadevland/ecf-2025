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
                <div class="alert alert-success mb-6">
                    <span class="text-lg">ℹ️</span>
                    <span>
                        <strong>Statut :</strong> {{ $reservation->getStatutLabel() }}
                        - Réservation valide
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
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-primary">🎬 {{ $reservation->seance->film->titre }}</h3>
                            <p class="text-base-content/70">{{ $reservation->seance->film->categorie }}</p>
                            <p class="text-sm text-base-content/50">{{ $reservation->seance->film->duree_minutes }} minutes</p>
                        </div>

                        <!-- Informations de la séance -->
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="font-semibold">🏢 Cinéma :</span>
                                <span>{{ $reservation->seance->salle->cinema->nom }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="font-semibold">🎭 Salle :</span>
                                <span>{{ $reservation->seance->salle->nom }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="font-semibold">📅 Date :</span>
                                <span>{{ $reservation->seance->date_heure_debut->format('d/m/Y à H\hi') }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="font-semibold">👥 Places :</span>
                                <span>{{ $reservation->nombre_places }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="font-semibold">💰 Prix :</span>
                                <span class="font-bold">{{ number_format($reservation->prix_total, 2) }} €</span>
                            </div>
                        </div>

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
                        <h3 class="text-lg font-bold mb-4">📋 Instructions</h3>
                        <ul class="space-y-2 text-sm">
                            <li>✅ Présentez-vous à l'accueil 15 minutes avant la séance</li>
                            <li>✅ Ayez votre QR code à portée de main</li>
                            <li>✅ Places garanties jusqu'à 10 minutes avant le début</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>