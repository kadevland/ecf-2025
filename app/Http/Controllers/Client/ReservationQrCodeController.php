<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Infrastructure\Services\QrCodeService;
use App\Models\Reservation;
use Illuminate\Http\Response;

final class ReservationQrCodeController extends Controller
{
    public function __construct(
        private QrCodeService $qrCodeService
    ) {}

    public function __invoke(Reservation $reservation): Response
    {
        // Générer le QR code pour la réservation
        $qrCodeDataUri = $this->qrCodeService->generateForReservation($reservation->uuid);

        // Extraire les données base64 du data URI
        $qrCodeData   = mb_substr($qrCodeDataUri, mb_strpos($qrCodeDataUri, ',') + 1);
        $qrCodeBinary = base64_decode($qrCodeData);

        return response($qrCodeBinary, 200, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => 'inline; filename="qrcode-reservation-'.$reservation->numero_reservation.'.png"',
            'Cache-Control'       => 'public, max-age=3600', // Cache pour 1 heure
        ]);
    }
}
