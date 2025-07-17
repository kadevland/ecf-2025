<?php

declare(strict_types=1);

namespace App\Application\Mappers\Http;

use App\Application\DTOs\Film\CreerFilmDto;
use App\Application\DTOs\Film\ModifierFilmDto;
use App\Domain\Enums\GenreFilm;
use App\Domain\Enums\QualiteProjection;
use App\Domain\ValueObjects\Film\DureeFilm;
use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\MapperBuilder;
use Illuminate\Http\Request;
use InvalidArgumentException;

final readonly class FilmHttpMapper
{
    private \CuyZ\Valinor\Mapper\TreeMapper $mapper;

    public function __construct()
    {
        $this->mapper = (new MapperBuilder())
            ->allowSuperfluousKeys()
            ->enableFlexibleCasting()
            ->mapper();
    }

    public function mapToCreerFilmDto(Request $request): CreerFilmDto
    {
        $data = $request->validated();

        $mappedData = [
            'titre'               => $data['titre'],
            'duree'               => $this->mapDuree($data['duree']),
            'genre'               => $this->mapGenre($data['genre']),
            'qualitesDisponibles' => $this->mapQualitesProjection($data['qualites_disponibles'] ?? []),
            'synopsis'            => $data['synopsis']           ?? null,
            'realisateur'         => $data['realisateur']        ?? null,
            'acteursPrincipaux'   => $data['acteurs_principaux'] ?? [],
            'anneeSortie'         => $data['annee_sortie']       ?? null,
            'dureeMinutes'        => $data['duree_minutes']      ?? null,
            'noteMoyenne'         => $data['note_moyenne']       ?? null,
            'nombreVotes'         => $data['nombre_votes']       ?? 0,
            'affiche'             => $data['affiche']            ?? null,
            'bandeAnnonce'        => $data['bande_annonce']      ?? null,
            'estVisible'          => $data['est_visible']        ?? true,
            'estEnSalle'          => $data['est_en_salle']       ?? false,
            'dateSortie'          => $data['date_sortie']        ?? null,
            'dateFinDiffusion'    => $data['date_fin_diffusion'] ?? null,
        ];

        try {
            return $this->mapper->map(CreerFilmDto::class, $mappedData);
        } catch (MappingError $e) {
            throw new InvalidArgumentException('Données invalides pour créer un film: '.$e->getMessage());
        }
    }

    public function mapToModifierFilmDto(Request $request, string $filmId): ModifierFilmDto
    {
        $data = $request->validated();

        $mappedData = [
            'filmId'              => $filmId,
            'titre'               => $data['titre'] ?? null,
            'duree'               => isset($data['duree']) ? $this->mapDuree($data['duree']) : null,
            'genre'               => isset($data['genre']) ? $this->mapGenre($data['genre']) : null,
            'qualitesDisponibles' => isset($data['qualites_disponibles']) ? $this->mapQualitesProjection($data['qualites_disponibles']) : null,
            'synopsis'            => $data['synopsis']           ?? null,
            'realisateur'         => $data['realisateur']        ?? null,
            'acteursPrincipaux'   => $data['acteurs_principaux'] ?? null,
            'anneeSortie'         => $data['annee_sortie']       ?? null,
            'dureeMinutes'        => $data['duree_minutes']      ?? null,
            'noteMoyenne'         => $data['note_moyenne']       ?? null,
            'nombreVotes'         => $data['nombre_votes']       ?? null,
            'affiche'             => $data['affiche']            ?? null,
            'bandeAnnonce'        => $data['bande_annonce']      ?? null,
            'estVisible'          => $data['est_visible']        ?? null,
            'estEnSalle'          => $data['est_en_salle']       ?? null,
            'dateSortie'          => $data['date_sortie']        ?? null,
            'dateFinDiffusion'    => $data['date_fin_diffusion'] ?? null,
        ];

        try {
            return $this->mapper->map(ModifierFilmDto::class, $mappedData);
        } catch (MappingError $e) {
            throw new InvalidArgumentException('Données invalides pour modifier un film: '.$e->getMessage());
        }
    }

    private function mapDuree(int|string $duree): DureeFilm
    {
        if (is_string($duree)) {
            // Format "2h30" ou "150min" ou "150"
            if (preg_match('/(\d+)h(\d+)/', $duree, $matches)) {
                $minutes = (int) $matches[1] * 60 + (int) $matches[2];
            } elseif (preg_match('/(\d+)min/', $duree, $matches)) {
                $minutes = (int) $matches[1];
            } else {
                $minutes = (int) $duree;
            }
        } else {
            $minutes = $duree;
        }

        return DureeFilm::fromMinutes($minutes);
    }

    private function mapGenre(string $genre): GenreFilm
    {
        return GenreFilm::tryFrom($genre) ?? GenreFilm::Autre;
    }

    /**
     * @return array<QualiteProjection>
     */
    private function mapQualitesProjection(array $qualites): array
    {
        return array_map(
            fn (string $qualite) => QualiteProjection::tryFrom($qualite) ?? QualiteProjection::Standard,
            $qualites
        );
    }
}
