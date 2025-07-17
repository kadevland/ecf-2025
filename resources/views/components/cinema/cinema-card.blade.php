@props(['cinema' => null, 'showActions' => false, 'size' => 'default', 'showHoraires' => true, 'showAccessibilite' => true])

<div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow {{ $sizeClass() }}">
    <div class="card-body">
        {{-- En-tête avec nom et statut --}}
        <div class="flex justify-between items-start mb-4">
            <h3 class="card-title text-xl">
                {{ $cinema->nom() }}
                @if($cinema->estActif())
                    <div class="badge {{ $cinema->classeBadgeStatut() }} badge-sm">
                        {{ $cinema->statutBadge() }}
                    </div>
                @else
                    <div class="badge {{ $cinema->classeBadgeStatut() }} badge-sm">
                        {{ $cinema->statutBadge() }}
                    </div>
                @endif
            </h3>
            
            @if($showActions)
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </div>
                    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow">
                        <li><a href="{{ $cinema->lienDetail() }}">Voir détails</a></li>
                        @if($cinema->peutEtreModifie())
                            <li><a href="{{ $cinema->lienModifier() }}">Modifier</a></li>
                        @endif
                        @if($cinema->peutEtreSupprime())
                            <li><a href="{{ $cinema->lienSupprimer() }}" class="text-error">Supprimer</a></li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>

        {{-- Description --}}
        @if($showFullInfo() && $cinema->description())
            <p class="text-base-content/70 mb-4">{{ $cinema->description() }}</p>
        @endif

        {{-- Informations principales --}}
        <div class="space-y-3">
            {{-- Adresse --}}
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium">{{ $cinema->adresseComplete() }}</p>
                    @if($showFullInfo())
                        <p class="text-xs text-base-content/60">{{ $cinema->ville() }} - {{ $cinema->pays() }}</p>
                    @endif
                </div>
            </div>

            {{-- Contact --}}
            @if($showFullInfo())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="text-sm">{{ $cinema->telephone() }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-sm">{{ $cinema->email() }}</span>
                    </div>
                </div>
            @endif

            {{-- Nombre de salles --}}
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span class="text-sm">{{ $cinema->nombreSalles() }} salle{{ $cinema->nombreSalles() > 1 ? 's' : '' }}</span>
            </div>

            {{-- Horaires d'ouverture --}}
            @if($showHoraires && $cinema->estOuvertAujourdhui())
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm text-success font-medium">Ouvert aujourd'hui : {{ $cinema->horairesDuJour() }}</span>
                </div>
            @elseif($showHoraires && !$cinema->estOuvertAujourdhui())
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm text-error">Fermé aujourd'hui</span>
                </div>
            @endif
        </div>

        {{-- Accessibilité --}}
        @if($showAccessibilite && !empty($cinema->iconesAccessibilite()))
            <div class="mt-4 pt-4 border-t border-base-300">
                <div class="flex flex-wrap gap-2">
                    @foreach($cinema->iconesAccessibilite() as $accessibilite)
                        <div class="tooltip" data-tip="{{ $accessibilite['label'] }}">
                            <div class="badge badge-outline badge-sm">
                                {{ $accessibilite['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Actions principales --}}
        @if(!$showActions)
            <div class="card-actions justify-end mt-4">
                <a href="{{ $cinema->lienDetail() }}" class="btn btn-primary btn-sm">
                    Voir les séances
                </a>
            </div>
        @endif
    </div>
</div>