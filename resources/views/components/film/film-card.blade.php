@props(['film' => null, 'showActions' => false, 'size' => 'default', 'showSynopsis' => true, 'showNote' => true, 'showSeances' => false])

<div class="card bg-base-100 shadow-lg hover:shadow-xl transition-all duration-300 {{ $sizeClass() }}">
    {{-- Affiche du film --}}
    <figure class="relative overflow-hidden">
        <img 
            src="{{ $film->affiche() }}" 
            alt="Affiche de {{ $film->titre() }}"
            class="w-full h-64 object-cover transition-transform duration-300 hover:scale-105"
            loading="lazy"
        >
        
        {{-- Badges overlay --}}
        <div class="absolute top-2 left-2 flex flex-col gap-2">
            <div class="badge {{ $film->classeBadgeStatut() }} badge-sm">
                {{ $film->badgeStatut() }}
            </div>
            @if($film->classificationAge())
                <div class="badge {{ $film->classeBadgeClassification() }} badge-sm">
                    {{ $film->badgeClassification() }}
                </div>
            @endif
        </div>

        {{-- Actions rapides --}}
        @if($showActions)
            <div class="absolute top-2 right-2">
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-sm btn-circle bg-black/20 hover:bg-black/40">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </div>
                    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow">
                        <li><a href="{{ $film->lienDetail() }}">Voir détails</a></li>
                        @if($film->peutEtreReserve())
                            <li><a href="{{ $film->lienReserver() }}">Réserver</a></li>
                        @endif
                        @if($film->peutEtreModifie())
                            <li><a href="{{ $film->lienAdminModifier() }}">Modifier</a></li>
                        @endif
                        @if($film->peutEtreSupprime())
                            <li><a href="{{ $film->lienAdminSupprimer() }}" class="text-error">Supprimer</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        @endif

        {{-- Note moyenne --}}
        @if($showNote && $film->noteMoyenne())
            <div class="absolute bottom-2 right-2 bg-black/70 text-white px-2 py-1 rounded-lg flex items-center gap-1">
                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <span class="text-sm font-medium">{{ $film->noteFormatee() }}</span>
            </div>
        @endif
    </figure>

    <div class="card-body p-4">
        {{-- Titre et année --}}
        <h3 class="card-title text-lg leading-tight">
            {{ $film->titre() }}
            <span class="text-sm text-base-content/60 font-normal">({{ $film->anneeSortie() }})</span>
        </h3>

        {{-- Genres et durée --}}
        <div class="flex flex-wrap items-center gap-2 mb-2">
            <span class="badge badge-outline badge-sm">{{ $film->categorie() }}</span>
            <span class="text-xs text-base-content/60">{{ $film->dureeFormatee() }}</span>
        </div>

        {{-- Synopsis --}}
        @if($showSynopsis && $showFullInfo())
            <p class="text-sm text-base-content/80 mb-3 leading-relaxed">
                {{ $film->synopsisResume($synopsisLength()) }}
            </p>
        @endif

        {{-- Informations supplémentaires --}}
        @if($showFullInfo())
            <div class="space-y-2 text-sm">
                {{-- Réalisateur --}}
                @if($film->realisateur())
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v2a1 1 0 01-1 1h-1v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8H3a1 1 0 01-1-1V5a1 1 0 011-1h4zM9 3v1h6V3H9zm2 8v6h2v-6h-2z"></path>
                        </svg>
                        <span class="text-base-content/70">{{ $film->realisateur() }}</span>
                    </div>
                @endif

                {{-- Acteurs principaux --}}
                @if(!empty($film->acteurs()))
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="text-base-content/70 text-xs">{{ $film->acteursFormates() }}</span>
                    </div>
                @endif
            </div>
        @endif

        {{-- Prochaines séances --}}
        @if($showSeances && $film->nombreSeancesAujourdhui() > 0)
            <div class="mt-3 pt-3 border-t border-base-300">
                <div class="flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-success font-medium">{{ $film->nombreSeancesAujourdhui() }} séance(s) aujourd'hui</span>
                </div>
            </div>
        @endif

        {{-- Actions --}}
        <div class="card-actions justify-end mt-4">
            @if($film->aBandeAnnonce())
                <button class="btn btn-ghost btn-sm" onclick="modal_trailer_{{ $film->id() }}.showModal()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m2-10v.01M3 20h18M4 20V10a6 6 0 1112 0v10"></path>
                    </svg>
                    Bande-annonce
                </button>
            @endif
            
            @if($film->peutEtreReserve())
                <a href="{{ $film->lienReserver() }}" class="btn btn-primary btn-sm">
                    Réserver
                </a>
            @else
                <a href="{{ $film->lienDetail() }}" class="btn btn-outline btn-sm">
                    Voir détails
                </a>
            @endif
        </div>
    </div>
</div>

{{-- Modal bande-annonce --}}
@if($film->aBandeAnnonce())
    <dialog id="modal_trailer_{{ $film->id() }}" class="modal">
        <div class="modal-box w-11/12 max-w-4xl">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="font-bold text-lg mb-4">Bande-annonce - {{ $film->titre() }}</h3>
            <div class="aspect-video">
                <iframe 
                    src="{{ $film->bandeAnnonce() }}" 
                    class="w-full h-full rounded-lg"
                    allowfullscreen
                ></iframe>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
@endif