<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

final readonly class PdfService
{
    public function __construct(
        private QrCodeService $qrCodeService
    ) {}

    public function generateReservationPdf(Reservation $reservation): Response
    {
        // Charger les relations nécessaires
        $reservation->load([
            'seance.film',
            'seance.salle.cinema',
            'billets',
        ]);

        // Générer le QR code
        $qrCodeDataUri = $this->qrCodeService->generateForReservation($reservation->uuid);

        // Données pour le PDF
        $data = [
            'reservation'   => $reservation,
            'qrCodeDataUri' => $qrCodeDataUri,
        ];

        // Générer le PDF
        $pdf = Pdf::loadView('pdf.reservation-ticket', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = "reservation-{$reservation->numero_reservation}.pdf";

        return $pdf->download($filename);
    }

    public function generateReservationPdfStream(Reservation $reservation): Response
    {
        // Charger les relations nécessaires
        $reservation->load([
            'seance.film',
            'seance.salle.cinema',
            'billets',
        ]);

        // Générer le QR code
        $qrCodeDataUri = $this->qrCodeService->generateForReservation($reservation->uuid);

        // Données pour le PDF
        $data = [
            'reservation'   => $reservation,
            'qrCodeDataUri' => $qrCodeDataUri,
        ];

        // Générer le PDF
        $pdf = Pdf::loadView('pdf.reservation-ticket', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = "reservation-{$reservation->numero_reservation}.pdf";

        return $pdf->stream($filename);
    }
}
