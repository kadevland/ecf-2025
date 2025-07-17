<?php

declare(strict_types=1);

namespace App\Domain\Entities\Film;

use App\Domain\Entities\ComponentEntity\ComponentEntity;
use App\Domain\Entities\EntityInterface;
use App\Domain\Entities\Film\Components\FilmComponents;
use App\Domain\Entities\Film\Components\ImagesFilm;
use App\Domain\Entities\Film\Components\RevuesPresse;
use App\Domain\Enums\CategorieFilm;
use App\Domain\Enums\QualiteProjection;
use App\Domain\Events\Film\FilmCreatedEvent;
use App\Domain\Traits\RecordsDomainEvents;
use App\Domain\ValueObjects\Film\FilmId;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final class Film implements EntityInterface
{
    use RecordsDomainEvents;

    public function __construct(
        public private(set) FilmId $id,
        public private(set) string $titre,
        public private(set) string $synopsis,
        public private(set) int $dureeMinutes,
        public private(set) CategorieFilm $categorie,
        /**
         * @var array<CategorieFilm>
         */
        public private(set) array $categories,
        public private(set) ?string $realisateur,
        /**
         * @var array<string>
         */
        public private(set) array $acteurs,
        /**
         * @var array<string>
         */
        public private(set) array $genres,
        /**
         * @var array<QualiteProjection>
         */
        public private(set) array $qualitesDisponibles,
        public private(set) FilmComponents $components,
        public private(set) CarbonImmutable $dateSortie,
        public private(set) bool $estActif,
        public private(set) CarbonImmutable $createdAt,
        public private(set) CarbonImmutable $updatedAt,
    ) {
        $this->enforceInvariants();
        $this->recordEvent(new FilmCreatedEvent($this->id, $this->titre, $this->dateSortie));
    }

    public function changerTitre(string $nouveauTitre): void
    {
        $this->validerTitre($nouveauTitre);

        if ($this->titre === $nouveauTitre) {
            return;
        }

        $this->titre = $nouveauTitre;
        $this->touch();
    }

    public function changerSynopsis(string $nouveauSynopsis): void
    {
        $this->validerSynopsis($nouveauSynopsis);

        if ($this->synopsis === $nouveauSynopsis) {
            return;
        }

        $this->synopsis = $nouveauSynopsis;
        $this->touch();
    }

    public function changerDuree(int $nouvellesDureeMinutes): void
    {
        $this->validerDuree($nouvellesDureeMinutes);

        if ($this->dureeMinutes === $nouvellesDureeMinutes) {
            return;
        }

        $this->dureeMinutes = $nouvellesDureeMinutes;
        $this->touch();
    }

    public function changerCategorie(CategorieFilm $nouvelleCategorie): void
    {
        if ($this->categorie === $nouvelleCategorie) {
            return;
        }

        // Retirer l'ancienne catégorie principale du tableau
        $this->categories = array_values(array_filter(
            $this->categories,
            fn (CategorieFilm $cat) => $cat !== $this->categorie
        ));

        // Mettre à jour la catégorie principale
        $this->categorie = $nouvelleCategorie;

        // Ajouter la nouvelle catégorie principale au tableau si pas déjà présente
        if (! in_array($nouvelleCategorie, $this->categories, true)) {
            array_unshift($this->categories, $nouvelleCategorie);
        }

        $this->touch();
    }

    /**
     * @param  array<CategorieFilm>  $nouvelles
     */
    public function changerCategories(array $nouvelles): void
    {
        if (empty($nouvelles)) {
            throw new InvalidArgumentException('Un film doit avoir au moins une catégorie');
        }

        $this->validerCategories($nouvelles);

        // La première devient la catégorie principale
        $this->categorie  = $nouvelles[0];
        $this->categories = $nouvelles;

        $this->touch();
    }

    public function ajouterCategorieSecondaire(CategorieFilm $categorie): void
    {
        if (in_array($categorie, $this->categories, true)) {
            return; // Déjà présente
        }

        $this->categories[] = $categorie;
        $this->touch();
    }

    public function supprimerCategorieSecondaire(CategorieFilm $categorie): void
    {
        if ($categorie === $this->categorie) {
            throw new InvalidArgumentException('Impossible de supprimer la catégorie principale');
        }

        $this->categories = array_values(array_filter(
            $this->categories,
            fn (CategorieFilm $cat) => $cat !== $categorie
        ));

        $this->touch();
    }

    /**
     * @return array<CategorieFilm>
     */
    public function getCategoriesSecondaires(): array
    {
        return array_values(array_filter(
            $this->categories,
            fn (CategorieFilm $cat) => $cat !== $this->categorie
        ));
    }

    public function changerRealisateur(?string $nouveauRealisateur): void
    {
        if ($nouveauRealisateur !== null) {
            $this->validerRealisateur($nouveauRealisateur);
        }

        if ($this->realisateur === $nouveauRealisateur) {
            return;
        }

        $this->realisateur = $nouveauRealisateur;
        $this->touch();
    }

    /**
     * @param  array<string>  $nouveauxActeurs
     */
    public function changerActeurs(array $nouveauxActeurs): void
    {
        $this->validerActeurs($nouveauxActeurs);

        if ($this->acteurs === $nouveauxActeurs) {
            return;
        }

        $this->acteurs = $nouveauxActeurs;
        $this->touch();
    }

    public function ajouterActeur(string $acteur): void
    {
        $this->validerActeur($acteur);

        if (in_array($acteur, $this->acteurs, true)) {
            return;
        }

        $this->acteurs[] = $acteur;
        $this->touch();
    }

    /**
     * @param  array<string>  $nouveauxGenres
     */
    public function changerGenres(array $nouveauxGenres): void
    {
        $this->validerGenres($nouveauxGenres);

        if ($this->genres === $nouveauxGenres) {
            return;
        }

        $this->genres = $nouveauxGenres;
        $this->touch();
    }

    public function ajouterGenre(string $genre): void
    {
        $this->validerGenre($genre);

        if (in_array($genre, $this->genres, true)) {
            return;
        }

        $this->genres[] = $genre;
        $this->touch();
    }

    /**
     * @param  array<QualiteProjection>  $nouvelles
     */
    public function changerQualitesDisponibles(array $nouvelles): void
    {
        if (empty($nouvelles)) {
            throw new InvalidArgumentException('Un film doit avoir au moins une qualité de projection disponible');
        }

        $this->qualitesDisponibles = [];
        foreach ($nouvelles as $qualite) {
            $this->ajouterQualiteDisponible($qualite);
        }
    }

    public function ajouterQualiteDisponible(QualiteProjection $qualite): void
    {
        if (in_array($qualite, $this->qualitesDisponibles, true)) {
            return;
        }

        $this->qualitesDisponibles[] = $qualite;
        $this->touch();
    }

    public function changerComponents(FilmComponents $nouveauxComponents): void
    {
        $this->components = $nouveauxComponents;
        $this->touch();
    }

    public function ajouterComponent(ComponentEntity $component): void
    {
        $this->components = $this->components->add($component);
        $this->touch();
    }

    /**
     * @param  class-string<ComponentEntity>  $componentClass
     */
    public function supprimerComponent(string $componentClass): void
    {
        $this->components = $this->components->remove($componentClass);
        $this->touch();
    }

    public function changerImages(ImagesFilm $nouvellesImages): void
    {
        $this->components = $this->components->withImages($nouvellesImages);
        $this->touch();
    }

    public function ajouterAffiche(string $urlAffiche): void
    {
        $images           = $this->components->getImages() ?? ImagesFilm::vide();
        $nouvellesImages  = $images->ajouterAffiche($urlAffiche);
        $this->components = $this->components->withImages($nouvellesImages);
        $this->touch();
    }

    public function ajouterCapture(string $urlCapture): void
    {
        $images           = $this->components->getImages() ?? ImagesFilm::vide();
        $nouvellesImages  = $images->ajouterCapture($urlCapture);
        $this->components = $this->components->withImages($nouvellesImages);
        $this->touch();
    }

    public function ajouterBandeAnnonce(string $urlBandeAnnonce): void
    {
        $images           = $this->components->getImages() ?? ImagesFilm::vide();
        $nouvellesImages  = $images->ajouterBandeAnnonce($urlBandeAnnonce);
        $this->components = $this->components->withImages($nouvellesImages);
        $this->touch();
    }

    public function changerRevuesPresse(RevuesPresse $nouvellesRevues): void
    {
        $this->components = $this->components->withRevuesPresse($nouvellesRevues);
        $this->touch();
    }

    public function ajouterRevuePresse(
        string $source,
        string $titre,
        string $extrait,
        ?float $note = null,
        ?string $url = null,
        ?CarbonImmutable $date = null
    ): void {
        $revues           = $this->components->getRevuesPresse() ?? RevuesPresse::vide();
        $nouvellesRevues  = $revues->ajouterRevue($source, $titre, $extrait, $note, $url, $date);
        $this->components = $this->components->withRevuesPresse($nouvellesRevues);
        $this->touch();
    }

    public function changerDateSortie(CarbonImmutable $nouvelleDateSortie): void
    {
        if ($this->dateSortie->equalTo($nouvelleDateSortie)) {
            return;
        }

        $this->dateSortie = $nouvelleDateSortie;
        $this->touch();
    }

    public function activer(): void
    {
        if ($this->estActif) {
            return;
        }

        $this->estActif = true;
        $this->touch();
    }

    public function desactiver(): void
    {
        if (! $this->estActif) {
            return;
        }

        $this->estActif = false;
        $this->touch();
    }

    public function estSortie(?CarbonImmutable $date = null): bool
    {
        $maintenant = $date ?? CarbonImmutable::now();

        return $this->dateSortie->lte($maintenant);
    }

    public function estDisponible(): bool
    {
        return $this->estActif && $this->estSortie();
    }

    public function supporteQualite(QualiteProjection $qualite): bool
    {
        return in_array($qualite, $this->qualitesDisponibles, true);
    }

    public function dureeEnHeures(): float
    {
        return round($this->dureeMinutes / 60, 2);
    }

    public function getAffichePrincipale(): ?string
    {
        $images = $this->components->getImages();

        return $images?->getAffichePrincipale();
    }

    public function getBandeAnnoncePrincipale(): ?string
    {
        $images = $this->components->getImages();

        return $images?->getBandeAnnoncePrincipale();
    }

    public function getNoteMovennePresse(): ?float
    {
        $revues = $this->components->getRevuesPresse();

        return $revues?->getNoteMovenne();
    }

    public function nombreRevuesPresse(): int
    {
        $revues = $this->components->getRevuesPresse();

        return $revues?->nombreRevues() ?? 0;
    }

    public function hasImages(): bool
    {
        return $this->components->has(ImagesFilm::class);
    }

    public function hasRevuesPresse(): bool
    {
        return $this->components->has(RevuesPresse::class);
    }

    public function equals(EntityInterface $other): bool
    {
        return $other instanceof self && $this->id->equals($other->id);
    }

    private function enforceInvariants(): void
    {
        $this->validerTitre($this->titre);
        $this->validerSynopsis($this->synopsis);
        $this->validerDuree($this->dureeMinutes);
        $this->validerCategories($this->categories);
        $this->validerActeurs($this->acteurs);
        $this->validerGenres($this->genres);

        if (empty($this->qualitesDisponibles)) {
            throw new InvalidArgumentException('Un film doit avoir au moins une qualité de projection disponible');
        }

        // Vérifier que la catégorie principale est dans le tableau
        if (! in_array($this->categorie, $this->categories, true)) {
            throw new InvalidArgumentException('La catégorie principale doit être présente dans le tableau des catégories');
        }

        if ($this->realisateur !== null) {
            $this->validerRealisateur($this->realisateur);
        }
    }

    private function validerTitre(string $titre): void
    {
        try {
            v::stringType()->notEmpty()
                ->length(1, 200)
                ->assert($titre);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Titre invalide: doit contenir entre 1 et 200 caractères');
        }
    }

    private function validerSynopsis(string $synopsis): void
    {
        try {
            v::stringType()->notEmpty()
                ->length(10, 2000)
                ->assert($synopsis);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Synopsis invalide: doit contenir entre 10 et 2000 caractères');
        }
    }

    private function validerDuree(int $dureeMinutes): void
    {
        if ($dureeMinutes < 1 || $dureeMinutes > 600) {
            throw new InvalidArgumentException('Durée invalide: doit être entre 1 et 600 minutes');
        }
    }

    private function validerRealisateur(string $realisateur): void
    {
        try {
            v::stringType()->notEmpty()
                ->length(1, 100)
                ->assert($realisateur);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Réalisateur invalide: doit contenir entre 1 et 100 caractères');
        }
    }

    /**
     * @param  array<string>  $acteurs
     */
    private function validerActeurs(array $acteurs): void
    {
        foreach ($acteurs as $acteur) {
            $this->validerActeur($acteur);
        }
    }

    private function validerActeur(string $acteur): void
    {
        try {
            v::stringType()->notEmpty()
                ->length(1, 100)
                ->assert($acteur);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Acteur invalide: doit contenir entre 1 et 100 caractères');
        }
    }

    /**
     * @param  array<string>  $genres
     */
    private function validerGenres(array $genres): void
    {
        if (empty($genres)) {
            throw new InvalidArgumentException('Un film doit avoir au moins un genre');
        }

        foreach ($genres as $genre) {
            $this->validerGenre($genre);
        }
    }

    private function validerGenre(string $genre): void
    {
        try {
            v::stringType()->notEmpty()
                ->length(1, 50)
                ->assert($genre);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Genre invalide: doit contenir entre 1 et 50 caractères');
        }
    }

    /**
     * @param  array<CategorieFilm>  $categories
     */
    private function validerCategories(array $categories): void
    {
        if (empty($categories)) {
            throw new InvalidArgumentException('Un film doit avoir au moins une catégorie');
        }

        foreach ($categories as $categorie) {
            // @phpstan-ignore instanceof.alwaysTrue
            if (! ($categorie instanceof CategorieFilm)) {
                throw new InvalidArgumentException('Catégorie invalide');
            }
        }

        // Vérifier les doublons
        $valeurs = array_map(fn (CategorieFilm $c) => $c->value, $categories);
        if (count($valeurs) !== count(array_unique($valeurs))) {
            throw new InvalidArgumentException('Catégories dupliquées détectées');
        }
    }

    private function touch(): void
    {
        $this->updatedAt = CarbonImmutable::now();
    }
}
