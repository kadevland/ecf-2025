<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewModels;

use App\Domain\Entities\Film\Film;
use App\Domain\Enums\QualiteProjection;
use Illuminate\Support\Str;

final readonly class FilmViewModel
{
    public const DATE_FORMAT = 'd/m/Y';

    public Film $film;

    public string $id;

    public string $titre;

    public string $synopsis;

    public string $duree;

    public string $categorie;

    public string $classeBadgeCategorie;

    public string $realisateur;

    public string $acteurs;

    public string $dateSortie;

    public string $noteMovenne;

    public string $affiche;

    public string $date;

    public string $qualitesProjection;

    public function __construct(Film $film)
    {
        $this->film                  = $film;
        $this->id                    = $film->id->uuid;
        $this->titre                 = $film->titre;
        $this->synopsis              = $film->synopsis;
        $this->duree                 = self::formatDuree($film);
        $this->categorie             = Str::ucfirst($film->categorie->label());
        $this->classeBadgeCategorie  = self::formatClasseBadgeCategorie($film);
        $this->realisateur           = $film->realisateur ?? 'N/A';
        $this->acteurs               = self::formatActeurs($film);
        $this->dateSortie            = $film->dateSortie->format(self::DATE_FORMAT);
        $this->noteMovenne           = self::formatNoteMovenne($film);
        $this->affiche               = self::formatAffiche($film);
        $this->date                  = $film->createdAt->format(self::DATE_FORMAT);
        $this->qualitesProjection    = self::formatQualitesProjection($film);
    }

    private static function formatDuree(Film $film): string
    {
        $heures  = intdiv($film->dureeMinutes, 60);
        $minutes = $film->dureeMinutes % 60;

        if ($heures > 0) {
            return sprintf('%dh%02d', $heures, $minutes);
        }

        return sprintf('%d min', $minutes);
    }

    private static function formatClasseBadgeCategorie(Film $film): string
    {
        return match ($film->categorie->value) {
            'action'          => 'bg-red-100 text-red-800',
            'aventure'        => 'bg-yellow-100 text-yellow-800',
            'comedie'         => 'bg-green-100 text-green-800',
            'drame'           => 'bg-blue-100 text-blue-800',
            'horreur'         => 'bg-purple-100 text-purple-800',
            'science_fiction' => 'bg-indigo-100 text-indigo-800',
            'thriller'        => 'bg-gray-100 text-gray-800',
            'romance'         => 'bg-pink-100 text-pink-800',
            'animation'       => 'bg-orange-100 text-orange-800',
            'documentaire'    => 'bg-teal-100 text-teal-800',
            default           => 'bg-gray-100 text-gray-800',
        };
    }

    private static function formatActeurs(Film $film): string
    {
        if (empty($film->acteurs)) {
            return 'N/A';
        }

        return implode(', ', array_slice($film->acteurs, 0, 3));
    }

    private static function formatNoteMovenne(Film $film): string
    {
        $note = $film->getNoteMovennePresse();

        if ($note === null) {
            return 'N/A';
        }

        return sprintf('%.1f/10', $note);
    }

    private static function formatAffiche(Film $film): string
    {
        return $film->getAffichePrincipale() ?? '/images/default-poster.jpg';
    }

    private static function formatQualitesProjection(Film $film): string
    {
        $qualites = $film->qualitesDisponibles;

        if (empty($qualites)) {
            return 'Standard';
        }

        $labels = array_map(
            fn (QualiteProjection $qualite): string => $qualite->label(),
            $qualites
        );

        return implode(', ', $labels);
    }
}
