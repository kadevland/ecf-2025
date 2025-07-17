@props(['reservation'])

<div id="qr-modal-{{ $reservation->uuid }}" class="modal">
    <div class="modal-box max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">QR Code - Réservation</h3>
            <button class="btn btn-sm btn-circle btn-ghost" onclick="document.getElementById('qr-modal-{{ $reservation->uuid }}').close()">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        <div class="text-center">
            <!-- Informations de la réservation -->
            <div class="mb-6">
                <div class="flex items-center justify-center mb-2">
                    <div class="avatar">
                        <div class="w-12 h-12 rounded-lg mr-3">
                            <img src="{{ $reservation->seance->film->affiche_url ?? 'https://via.placeholder.com/48x48' }}" alt="Affiche du film" class="object-cover">
                        </div>
                    </div>
                    <div class="text-left">
                        <p class="font-bold">{{ $reservation->seance->film->titre }}</p>
                        <p class="text-sm text-base-content/70">{{ $reservation->seance->salle->cinema->nom }}</p>
                    </div>
                </div>
                <div class="text-sm text-base-content/70">
                    <p>{{ $reservation->seance->date_heure_debut->format('d/m/Y à H\hi') }}</p>
                    <p>Salle {{ $reservation->seance->salle->nom }} - {{ $reservation->nombre_places }} place(s)</p>
                    <div class="flex flex-wrap gap-1 justify-center mt-2">
                        @foreach($reservation->billets as $billet)
                            <span class="badge badge-primary badge-sm">{{ $billet->place }}</span>
                        @endforeach
                    </div>
                    <p class="font-semibold mt-2">N° {{ $reservation->numero_reservation }}</p>
                </div>
            </div>

            <!-- QR Code -->
            <div class="mb-6">
                <div class="bg-white p-4 rounded-lg shadow-inner mx-auto inline-block">
                    <img
                        src="{{ route('reservations.qr-code', $reservation->uuid) }}"
                        alt="QR Code pour la réservation {{ $reservation->numero_reservation }}"
                        class="mx-auto"
                        style="width: 200px; height: 200px;"
                    >
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
                <button
                    class="btn btn-primary btn-sm"
                    onclick="downloadQRCode('{{ $reservation->uuid }}', '{{ $reservation->numero_reservation }}')"
                >
                    <x-lucide-download class="w-4 h-4 mr-2" />
                    Télécharger
                </button>
                <button
                    class="btn btn-outline btn-sm"
                    onclick="printQRCode('{{ $reservation->uuid }}')"
                >
                    <x-lucide-printer class="w-4 h-4 mr-2" />
                    Imprimer
                </button>
            </div>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>Fermer</button>
    </form>
</div>

<script>
function downloadQRCode(reservationUuid, numeroReservation) {
    const qrImg = document.querySelector(`#qr-modal-${reservationUuid} img`);
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
    const modalContent = document.querySelector(`#qr-modal-${reservationUuid} .modal-box`);
    if (modalContent) {
        const printWindow = window.open('', '_blank');
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
                    ${modalContent.innerHTML}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
}
</script>
