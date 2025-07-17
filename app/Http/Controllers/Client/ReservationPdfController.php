<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Infrastructure\Services\PdfService;
use App\Models\Reservation;
use Illuminate\Http\Response;

final class ReservationPdfController extends Controller
{
    public function __construct(
        private PdfService $pdfService
    ) {}

    public function __invoke(Reservation $reservation): Response
    {
        return $this->pdfService->generateReservationPdf($reservation);
    }
}
