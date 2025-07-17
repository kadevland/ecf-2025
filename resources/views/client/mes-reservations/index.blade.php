<x-app.layout title="Mes Réservations">
    <div class="min-h-screen bg-base-200">
        <!-- Hero Section -->
        <div class="hero bg-primary text-primary-content">
            <div class="hero-content text-center py-16">
                <div class="max-w-md">
                    <h1 class="text-5xl font-bold">Mes Réservations</h1>
                    <p class="py-6">Consultez l'historique de vos réservations et vos billets</p>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-6xl mx-auto">
                <!-- Filtres - Toujours visibles -->
                <div class="card bg-base-100 shadow-xl mb-8">
                    <div class="card-body">
                        <form method="GET">
                            <div class="flex flex-wrap gap-4 items-center">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Filtrer par statut</span>
                                    </label>
                                    <select name="statut" class="select select-bordered" onchange="this.form.submit()">
                                        <option value="">Toutes</option>
                                        <option value="en_attente"
                                            {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente
                                        </option>
                                        <option value="confirmee"
                                            {{ request('statut') == 'confirmee' ? 'selected' : '' }}>Confirmée</option>
                                        <option value="payee" {{ request('statut') == 'payee' ? 'selected' : '' }}>
                                            Payée</option>
                                        <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>
                                            Annulée</option>
                                        <option value="utilisee"
                                            {{ request('statut') == 'utilisee' ? 'selected' : '' }}>Utilisée</option>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Trier par</span>
                                    </label>
                                    <select name="sort" class="select select-bordered" onchange="this.form.submit()">
                                        <option value="recent"
                                            {{ request('sort', 'recent') == 'recent' ? 'selected' : '' }}>Plus récent
                                        </option>
                                        <option value="ancien" {{ request('sort') == 'ancien' ? 'selected' : '' }}>Plus
                                            ancien</option>
                                        <option value="seance" {{ request('sort') == 'seance' ? 'selected' : '' }}>Par
                                            date de séance</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @if ($reservations->isEmpty())
                    <!-- État vide -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body text-center py-16">
                            <div class="mb-8">
                                <x-lucide-ticket class="w-24 h-24 mx-auto text-base-300" />
                            </div>
                            <h2 class="text-2xl font-bold mb-4">Aucune réservation trouvée</h2>
                            <p class="text-base-content/70 mb-8">
                                @if (request()->has('statut') && request('statut') != '')
                                    Aucune réservation avec le statut "{{ request('statut') }}" n'a été trouvée.
                                @else
                                    Vous n'avez pas encore effectué de réservation. Découvrez nos films à l'affiche et
                                    réservez votre première séance !
                                @endif
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                @if (request()->has('statut') && request('statut') != '')
                                    <a href="{{ route('mes-reservations') }}" class="btn btn-primary">
                                        <x-lucide-filter-x class="w-5 h-5 mr-2" />
                                        Effacer les filtres
                                    </a>
                                @endif
                                <a href="{{ route('films.index') }}" class="btn btn-primary">
                                    <x-lucide-film class="w-5 h-5 mr-2" />
                                    Voir les films
                                </a>
                                <a href="{{ route('cinemas.index') }}" class="btn btn-outline">
                                    <x-lucide-map-pin class="w-5 h-5 mr-2" />
                                    Nos cinémas
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Liste des réservations -->
                    <div class="space-y-6">
                        @foreach ($reservations as $reservation)
                            <div class="card bg-base-100 shadow-xl">
                                <div class="card-body">
                                    <div
                                        class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                                        <!-- Informations du film -->
                                        <div class="flex-1">
                                            <div class="flex items-center gap-4">
                                                <div class="avatar">
                                                    <div class="w-16 h-16 rounded-lg">
                                                        <img src="{{ $reservation->seance->film->affiche_url ?? 'https://via.placeholder.com/64x64' }}"
                                                            alt="Affiche du film" class="object-cover">
                                                    </div>
                                                </div>
                                                <div>
                                                    <h3 class="text-xl font-bold">
                                                        {{ $reservation->seance->film->titre }}</h3>
                                                    <p class="text-base-content/70">
                                                        {{ $reservation->seance->salle->cinema->nom }}</p>
                                                    <p class="text-sm text-base-content/50">
                                                        {{ $reservation->seance->date_heure_debut->format('d/m/Y à H\hi') }}
                                                        -
                                                        Salle {{ $reservation->seance->salle->nom }}
                                                    </p>
                                                    <div class="flex flex-wrap gap-1 mt-2">
                                                        @foreach ($reservation->billets as $billet)
                                                            <span
                                                                class="badge badge-primary badge-sm">{{ $billet->place }}</span>
                                                        @endforeach
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- Détails de la réservation -->
                                        <div class="text-right">
                                            <div class="badge {{ $reservation->getStatutBadgeClass() }} mb-2">
                                                {{ $reservation->getStatutLabel() }}</div>
                                            <p class="text-sm text-base-content/70">
                                                {{ $reservation->nombre_places }} place(s) -
                                                {{ $reservation->getMontantTotalFormate() }}
                                            </p>
                                            <p class="text-xs text-base-content/50">
                                                Réservé le {{ $reservation->created_at->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="card-actions justify-end mt-4" x-data="{ modalOpen: false }">
                                        <a href="{{ route('reservation.select-seats', $reservation->seance->sqid) }}"
                                            class="btn btn-primary btn-sm">
                                            <x-lucide-eye class="w-4 h-4 mr-2" />
                                            Voir la séance
                                        </a>
                                        <button class="btn btn-outline btn-sm" @click="modalOpen = true">
                                            <x-lucide-qr-code class="w-4 h-4 mr-2" />
                                            QR Code
                                        </button>
                                        <a href="{{ route('reservations.pdf', $reservation->uuid) }}"
                                            class="btn btn-outline btn-sm" target="_blank">
                                            <x-lucide-download class="w-4 h-4 mr-2" />
                                            PDF
                                        </a>
                                        @if ($reservation->canBeCancelled())
                                            <button class="btn btn-error btn-sm">
                                                <x-lucide-x class="w-4 h-4 mr-2" />
                                                Annuler
                                            </button>
                                        @endif

                                        <!-- QR Code Modal -->
                                        <div x-show="modalOpen" x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                            :class="modalOpen ? 'modal modal-open' : 'modal'"
                                            @click="modalOpen = false">
                                            <div class="modal-box max-w-md" @click.stop>
                                                <div class="flex justify-between items-center mb-4">
                                                    <h3 class="text-lg font-bold">QR Code - Réservation</h3>
                                                    <button class="btn btn-sm btn-circle btn-ghost"
                                                        @click="modalOpen = false">
                                                        <x-lucide-x class="w-4 h-4" />
                                                    </button>
                                                </div>

                                                <div class="text-center">
                                                    <!-- Informations de la réservation -->
                                                    <div class="mb-6">
                                                        <div class="flex items-center justify-center mb-2">
                                                            <div class="avatar">
                                                                <div class="w-12 h-12 rounded-lg mr-3">
                                                                    <img src="{{ $reservation->seance->film->affiche_url ?? 'https://via.placeholder.com/48x48' }}"
                                                                        alt="Affiche du film" class="object-cover">
                                                                </div>
                                                            </div>
                                                            <div class="text-left">
                                                                <p class="font-bold">
                                                                    {{ $reservation->seance->film->titre }}</p>
                                                                <p class="text-sm text-base-content/70">
                                                                    {{ $reservation->seance->salle->cinema->nom }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="text-sm text-base-content/70">
                                                            <p>{{ $reservation->seance->date_heure_debut->format('d/m/Y à H\hi') }}
                                                            </p>
                                                            <p>Salle {{ $reservation->seance->salle->nom }} -
                                                                {{ $reservation->nombre_places }} place(s)</p>
                                                            <p class="font-semibold">N°
                                                                {{ $reservation->numero_reservation }}</p>
                                                            <div class="flex flex-wrap gap-1 justify-center mt-2">
                                                                @foreach ($reservation->billets as $billet)
                                                                    <span
                                                                        class="badge badge-primary badge-sm">{{ $billet->place }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- QR Code -->
                                                    <div class="mb-6">
                                                        <div
                                                            class="bg-white p-4 rounded-lg shadow-inner mx-auto inline-block">
                                                            <img src="{{ route('reservations.qr-code', $reservation->uuid) }}"
                                                                alt="QR Code pour la réservation {{ $reservation->numero_reservation }}"
                                                                class="mx-auto" style="width: 200px; height: 200px;">
                                                        </div>
                                                    </div>

                                                    <!-- Instructions -->
                                                    <div class="text-sm text-base-content/70 mb-6">
                                                        <p class="font-semibold mb-2">Instructions :</p>
                                                        <ul class="text-left space-y-1">
                                                            <li>• Présentez ce QR code à l'entrée du cinéma</li>
                                                            <li>• Arrivez 15 minutes avant le début de la séance</li>
                                                            <li>• Gardez votre téléphone chargé ou imprimez ce code</li>
                                                        </ul>
                                                    </div>

                                                    <!-- Actions -->
                                                    <div class="flex gap-2 justify-center">
                                                        <button class="btn btn-primary btn-sm"
                                                            onclick="downloadQRCode('{{ $reservation->uuid }}', '{{ $reservation->numero_reservation }}')">
                                                            <x-lucide-download class="w-4 h-4 mr-2" />
                                                            Télécharger
                                                        </button>
                                                        <button class="btn btn-outline btn-sm"
                                                            onclick="printQRCode('{{ $reservation->uuid }}')">
                                                            <x-lucide-printer class="w-4 h-4 mr-2" />
                                                            Imprimer
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $reservations->links('pagination.daisyui') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function downloadQRCode(reservationUuid, numeroReservation) {
            const qrImg = document.querySelector(`img[src*="${reservationUuid}"]`);
            if (qrImg) {
                const link = document.createElement('a');
                link.download = `qrcode-reservation-${numeroReservation}.png`;
                link.href = qrImg.src;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }

        function printQRCode(reservationUuid) {
            const printWindow = window.open('', '_blank');
            const qrImg = document.querySelector(`img[src*="${reservationUuid}"]`);

            if (qrImg) {
                printWindow.document.write(`
                <html>
                    <head>
                        <title>QR Code - Réservation</title>
                        <style>
                            body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
                            .qr-container { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                            img { max-width: 200px; height: auto; }
                        </style>
                    </head>
                    <body>
                        <div class="qr-container">
                            <h2>QR Code - Réservation</h2>
                            <img src="${qrImg.src}" alt="QR Code">
                            <p>Présentez ce QR code à l'entrée du cinéma</p>
                        </div>
                    </body>
                </html>
            `);
                printWindow.document.close();
                printWindow.print();
            }
        }
    </script>
</x-app.layout>
