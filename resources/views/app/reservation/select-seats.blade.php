<x-app.layout title="Sélection des places">
    <!-- Hero avec informations de la séance -->
    <section class="bg-primary text-primary-content py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-8 items-start">
                <!-- Affiche du film -->
                <div class="flex-shrink-0">
                    <img src="{{ $seance['film']['affiche'] }}" alt="{{ $seance['film']['titre'] }}"
                        class="w-32 h-48 object-cover rounded-lg shadow-lg" />
                </div>

                <!-- Informations de la séance -->
                <div class="flex-1">
                    <h1 class="text-3xl font-bold mb-4">{{ $seance['film']['titre'] }}</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-primary-content/70">Cinéma:</span>
                            <span class="font-semibold ml-2">{{ $seance['cinema']['nom'] }}</span>
                        </div>
                        <div>
                            <span class="text-primary-content/70">Salle:</span>
                            <span class="font-semibold ml-2">{{ $seance['salle']['nom'] }}</span>
                        </div>
                        <div>
                            <span class="text-primary-content/70">Date et heure:</span>
                            <span class="font-semibold ml-2">{{ $seance['date_heure'] }}</span>
                        </div>
                        <div>
                            <span class="text-primary-content/70">Version:</span>
                            <span class="font-semibold ml-2">{{ $seance['version'] }}</span>
                        </div>
                        <div>
                            <span class="text-primary-content/70">Qualité:</span>
                            <span class="font-semibold ml-2">{{ $seance['qualite'] }}</span>
                        </div>
                        <div>
                            <span class="text-primary-content/70">Prix:</span>
                            <span class="font-semibold ml-2">{{ number_format($seance['prix_base'], 2, ',', ' ') }}
                                €</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sélection des places OU Message d'erreur -->
    <section class="py-12">
        <div class="container mx-auto px-4 max-w-6xl">
            @if ($reservationPossible)
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold mb-4">Sélectionnez vos places</h2>
                    <p class="text-base-content/70">Cliquez sur les sièges disponibles pour les sélectionner</p>

                    @if (session('error'))
                        <div class="alert alert-error max-w-2xl mx-auto mt-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold mb-4">Réservation impossible</h2>
                    <div class="alert alert-error max-w-2xl mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $messageErreur }}</span>
                    </div>
                </div>
            @endif

            @if ($reservationPossible)
                <!-- Légende -->
                <div class="flex flex-wrap justify-center gap-6 mb-8 p-4 bg-base-200 rounded-lg">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-success rounded border border-success-content"></div>
                        <span class="text-sm">Disponible</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-warning rounded border border-warning-content"></div>
                        <span class="text-sm">PMR</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-error rounded border border-error-content"></div>
                        <span class="text-sm">Occupé</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-info rounded border border-info-content border-2"></div>
                        <span class="text-sm">Sélectionné</span>
                    </div>
                </div>

                <!-- Formulaire englobant tout -->
                <form action="{{ route('reservation.confirm') }}" method="POST" x-data="seatSelection({{ $seance['prix_base'] }})">
                    @csrf
                    <input type="hidden" name="seance_id" value="{{ $seance['id'] }}">

                    <!-- Plan de salle -->
                    <div class="bg-white p-8 rounded-xl shadow-lg border">
                        <!-- Écran -->
                        <div class="text-center mb-8">
                            <div
                                class="bg-gradient-to-r from-primary/20 to-secondary/20 rounded-full h-3 w-3/4 mx-auto mb-2">
                            </div>
                            <p class="text-sm text-base-content/60 uppercase tracking-wider">Écran</p>
                        </div>

                        <!-- Sièges -->
                        <div class="space-y-4">
                            @foreach ($planSalle as $rangee)
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Lettre de rangée -->
                                    <div class="w-8 text-center font-bold text-base-content/70">
                                        {{ $rangee['rangee'] }}
                                    </div>
                                    <!-- Sièges de la rangée -->
                                    <div class="flex gap-1">
                                        @foreach ($rangee['sieges'] as $siege)
                                            <label
                                                class="relative @if (!$siege['occupe']) cursor-pointer @else cursor-not-allowed @endif">
                                                <input type="checkbox" name="seats[]" value="{{ $siege['id'] }}"
                                                    @if ($siege['occupe']) disabled @endif
                                                    x-model="selectedSeats" class="peer sr-only">
                                                <div
                                                    class="w-8 h-8 rounded border text-xs font-semibold transition-all duration-200 flex items-center justify-center
                                    @if ($siege['occupe']) bg-error border-error-content cursor-not-allowed opacity-60
                                    @elseif($siege['pmr'])
                                        bg-warning border-warning-content hover:bg-warning/80 peer-checked:bg-info peer-checked:border-info-content peer-checked:border-2
                                    @else
                                        bg-success border-success-content hover:bg-success/80 peer-checked:bg-info peer-checked:border-info-content peer-checked:border-2 @endif
                                    @if (!$siege['occupe']) peer-focus:ring-2 peer-focus:ring-offset-1 peer-focus:ring-primary @endif">
                                                    {{ $siege['numero'] }}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>

                                    <!-- Lettre de rangée (droite) -->
                                    <div class="w-8 text-center font-bold text-base-content/70">
                                        {{ $rangee['rangee'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Résumé de la sélection -->
                    <div class="mt-8 bg-base-200 rounded-lg p-6">
                        <h3 class="font-bold text-lg mb-4">Résumé de votre sélection</h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <span class="text-sm text-base-content/70">Places sélectionnées:</span>
                                <div class="font-semibold"
                                    x-text="selectedSeats.length > 0 ? selectedSeats.join(', ') : 'Aucune'"></div>
                            </div>
                            <div>
                                <span class="text-sm text-base-content/70">Nombre de places:</span>
                                <div class="font-semibold" x-text="selectedSeats.length"></div>
                            </div>
                            <div>
                                <span class="text-sm text-base-content/70">Prix total:</span>
                                <div class="font-bold text-lg text-primary"
                                    x-text="totalPrice.toFixed(2).replace('.', ',') + ' €'"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="label">
                                <span class="label-text">Places sélectionnées:</span>
                            </label>
                            <div class="flex flex-wrap gap-2 p-3 bg-base-200 rounded-lg"
                                x-show="selectedSeats.length > 0">
                                <template x-for="seat in selectedSeats" :key="seat">
                                    <span class="badge badge-primary" x-text="seat"></span>
                                </template>
                            </div>
                            <div x-show="selectedSeats.length === 0" class="text-base-content/70 text-center py-4">
                                Aucune place sélectionnée
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 justify-end">
                            <a href="{{ url()->previous() }}" class="btn btn-outline">
                                Retour
                            </a>
                            <button type="submit" class="btn btn-primary" :disabled="selectedSeats.length === 0">
                                Continuer vers la confirmation
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <!-- Section des suggestions alternatives -->
                <div class="max-w-4xl mx-auto">
                    @if (isset($suggestions['autresSeances']) && $suggestions['autresSeances']->isNotEmpty())
                        <div class="bg-base-100 rounded-lg p-6 shadow-lg mb-8">
                            <h3 class="text-2xl font-bold mb-6 text-center">Autres séances disponibles</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($suggestions['autresSeances'] as $autreSeance)
                                    <div class="card bg-base-200 shadow-md hover:shadow-lg transition-shadow">
                                        <div class="card-body">
                                            <h4 class="font-semibold text-lg">{{ $autreSeance['date_heure'] }}</h4>
                                            <p class="text-sm text-base-content/70 mb-2">{{ $autreSeance['cinema'] }}
                                                - {{ $autreSeance['salle'] }}</p>
                                            <p class="text-sm mb-4">
                                                <span
                                                    class="badge badge-success badge-sm">{{ $autreSeance['places_disponibles'] }}
                                                    places</span>
                                            </p>
                                            <div class="card-actions justify-end">
                                                <a href="{{ route('reservation.select-seats', $autreSeance['sqid']) }}"
                                                    class="btn btn-primary btn-sm">
                                                    Choisir cette séance
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Actions de retour -->
                    <div class="text-center">
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ $suggestions['retourFilm'] }}" class="btn btn-outline btn-primary">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Retour au film
                            </a>
                            <a href="{{ route('films.index') }}" class="btn btn-outline">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 4V2C7 1.45 7.45 1 8 1h8c.55 0 1 .45 1 1v2h5c.55 0 1 .45 1 1s-.45 1-1 1h-1v14c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V6H2c-.55 0-1-.45-1-1s.45-1 1-1h5z" />
                                </svg>
                                Voir tous les films
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Script Alpine.js pour la sélection -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('seatSelection', (basePrice) => ({
                selectedSeats: [],
                basePrice: basePrice,

                get totalPrice() {
                    return this.selectedSeats.length * this.basePrice;
                },

                init() {
                    // Surveiller les changements pour limiter à 8 places
                    this.$watch('selectedSeats', (value) => {
                        if (value.length > 8) {
                            alert('Vous ne pouvez sélectionner que 8 places maximum.');
                            // Retirer la dernière sélection
                            this.selectedSeats = value.slice(0, 8);
                        }
                    });
                }
            }))
        })
    </script>
</x-app.layout>
