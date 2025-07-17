<div class="overflow-x-auto">
    <table class="table table-zebra w-full">
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Place</th>
                <th>Tarif</th>
                <th>Prix</th>
                <th>Statut</th>
                <th>Réservation</th>
                <th>Séance</th>
                <th>QR Code</th>
                <th>Date création</th>
            </tr>
        </thead>
        <tbody>
            @foreach($viewPage->billetList->billets as $billet)
                <tr>
                    <td class="font-mono">{{ $billet->numeroBillet }}</td>
                    <td class="font-semibold">{{ $billet->place }}</td>
                    <td>{{ $billet->typeTarif }}</td>
                    <td class="font-semibold">{{ $billet->prix }}</td>
                    <td>{!! $billet->utiliseBadge !!}</td>
                    <td class="text-sm">{{ $billet->reservationInfo }}</td>
                    <td class="text-sm">{{ $billet->seanceInfo }}</td>
                    <td>
                        @if($billet->hasQrCode())
                            <span class="badge badge-info">QR</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="text-sm">{{ $billet->createdAt }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($viewPage->billetList->pagination)
    <div class="mt-6">
        {{ $viewPage->billetList->pagination->links() }}
    </div>
@endif