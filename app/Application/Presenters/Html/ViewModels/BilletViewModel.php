<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewModels;

use App\Domain\Entities\Billet\Billet;
use Illuminate\Support\Str;

final readonly class BilletViewModel
{
    public const DATE_FORMAT = 'd/m/Y';

    public const TIME_FORMAT = 'H:i';

    // ✅ Propriétés publiques déjà formatées pour la vue
    public Billet $billet;

    public string $id;

    public string $numeroBillet;

    public string $place;

    public string $typeTarif;

    public string $prix;

    public string $statutUtilisation;

    public string $classeBadgeStatut;

    public bool $estUtilise;

    public bool $peutEtreUtilise;

    public ?string $qrCode;

    public ?string $dateUtilisation;

    public string $date;

    public function __construct(Billet $billet)
    {
        $this->billet       = $billet;
        $this->id           = $billet->id?->uuid ?? '';
        $this->numeroBillet = $billet->numeroBillet;
        $this->place        = $billet->place;

        // ✅ Toutes les propriétés calculées dans le constructeur
        $this->typeTarif         = $this->formatTypeTarif($billet);
        $this->prix              = $this->formatPrix($billet);
        $this->statutUtilisation = $this->formatStatutUtilisation($billet);
        $this->classeBadgeStatut = $this->formatClasseBadgeStatut($billet);
        $this->estUtilise        = $billet->utilise;
        $this->peutEtreUtilise   = $billet->peutEtreUtilise();
        $this->qrCode            = $billet->qrCode;
        $this->dateUtilisation   = $billet->dateUtilisation?->format(self::DATE_FORMAT.' '.self::TIME_FORMAT);
        $this->date              = $billet->createdAt->format(self::DATE_FORMAT);
    }

    // ✅ Fonctions privées d'aide au formatage
    private function formatTypeTarif(Billet $billet): string
    {
        return match ($billet->typeTarif) {
            default => Str::ucfirst($billet->typeTarif->label()),
        };
    }

    private function formatPrix(Billet $billet): string
    {
        return $billet->prix->formatterAvecDevise();
    }

    private function formatStatutUtilisation(Billet $billet): string
    {
        return $billet->utilise ? 'Utilisé' : 'Non utilisé';
    }

    private function formatClasseBadgeStatut(Billet $billet): string
    {
        return $billet->utilise
            ? 'bg-gray-100 text-gray-800'
            : 'bg-green-100 text-green-800';
    }
}
