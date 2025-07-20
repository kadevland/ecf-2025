<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDF;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

final readonly class PdfService
{
    public function __construct(
        private QrCodeService $qrCodeService
    ) {}

    public function generateReservationPdf(Reservation $reservation): Response
    {
        $pdf = $this->buildPdf($reservation);
        $filename = $this->getNamePdf($reservation);

        return $pdf->download($filename);
    }

    public function generateReservationPdfStream(Reservation $reservation): Response
    {
        $pdf = $this->buildPdf($reservation);
        $filename = $this->getNamePdf($reservation);

        return $pdf->stream($filename);
    }

    /**
     * Construire le PDF avec toutes les données nécessaires
     */
    private function buildPdf(Reservation $reservation): DomPDF
    {
        // Charger les relations nécessaires
        $reservation->load([
            'seance.film',
            'seance.salle.cinema',
            'billets',
        ]);

        // Générer le QR code
        $qrCodeDataUri = $this->qrCodeService->generateForReservation($reservation->uuid);

        // Convertir l'affiche du film en base64
        $afficheDataUri = $this->convertImageToBase64($reservation->seance->film->affiche_url);

        // Données pour le PDF
        $data = [
            'reservation'   => $reservation,
            'qrCodeDataUri' => $qrCodeDataUri,
            'afficheDataUri' => $afficheDataUri,
        ];

        // Générer le PDF
        $pdf = Pdf::loadView('pdf.reservation-ticket', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Obtenir le nom du fichier PDF
     */
    private function getNamePdf(Reservation $reservation): string
    {
        return "reservation-{$reservation->numero_reservation}.pdf";
    }

    /**
     * Convertir une image externe en base64 Data URI
     */
    private function convertImageToBase64(?string $imageUrl): ?string
    {
        if (!$imageUrl) {
            return null;
        }

        try {
            // Télécharger l'image avec HTTP client Laravel
            $response = Http::timeout(10)->get($imageUrl);
            
            if ($response->successful()) {
                $imageContent = $response->body();
                $mimeType = $response->header('Content-Type') ?? 'image/jpeg';
                
                // Convertir en base64 Data URI
                return 'data:' . $mimeType . ';base64,' . base64_encode($imageContent);
            }
        } catch (\Exception $e) {
            // En cas d'erreur, retourner null (le template gérera l'absence d'image)
            logger()->warning('Impossible de télécharger l\'affiche pour le PDF', [
                'url' => $imageUrl,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}
