<?php

declare(strict_types=1);

namespace App\Domain\Entities\Cinema;

use App\Domain\Entities\EntityInterface;
use App\Domain\Enums\Pays;
use App\Domain\Enums\StatusCinema;
use App\Domain\Events\Cinema\CinemaCreatedEvent;
use App\Domain\Events\Cinema\SalleAjouteeEvent;
use App\Domain\Traits\ManagesOpeningHours;
use App\Domain\Traits\RecordsDomainEvents;
use App\Domain\ValueObjects\Cinema\CinemaId;
use App\Domain\ValueObjects\Commun\Adresse;
use App\Domain\ValueObjects\Commun\CoordonneesGPS;
use App\Domain\ValueObjects\Commun\Email;
use App\Domain\ValueObjects\Commun\Telephone;
use App\Domain\ValueObjects\Salle\SalleId;
use BadMethodCallException;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;
use Spatie\OpeningHours\OpeningHours;

final class Cinema implements EntityInterface
{
    use ManagesOpeningHours, RecordsDomainEvents;

    public function __construct(
        public private(set) CinemaId $id,
        public private(set) string $codeCinema,
        public private(set) string $nom,
        public private(set) Adresse $adresse,
        public private(set) Telephone $telephone,
        public private(set) Email $emailContact,
        public private(set) OpeningHours $horairesOuverture,
        /**
         * @var array<SalleId>
         */
        public private(set) array $salleIds,
        public private(set) StatusCinema $statut,
        public private(set) CoordonneesGPS $coordonneesGPS,
        public private(set) CarbonImmutable $createdAt,
        public private(set) CarbonImmutable $updatedAt,
    ) {
        $this->enforceInvariants();

        if ($this->id->isNew()) {

            $this->recordEvent(new CinemaCreatedEvent($this->id, $this->nom, $this->adresse));
        }
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'estOperationnel' => $this->estOperationnel(),
            default           => throw new BadMethodCallException("Property {$name} does not exist"),
        };
    }

    public function changerNom(string $nouveauNom): void
    {
        $this->validerNom($nouveauNom);

        if ($this->nom === $nouveauNom) {
            return;
        }

        $this->nom = $nouveauNom;
        $this->touch();
    }

    public function changerVille(string $ville): void
    {
        $this->adresse = $this->adresse->withVille($ville);
        $this->touch();
    }

    public function changerCodePostal(string $codePostal): void
    {
        $this->adresse = $this->adresse->withCodePostal($codePostal);
        $this->touch();
    }

    public function changerRue(string $rue): void
    {
        $this->adresse = $this->adresse->withRue($rue);
        $this->touch();
    }

    public function changerPays(Pays $pays): void
    {
        $this->adresse = $this->adresse->withPays($pays);
        $this->touch();
    }

    public function changerAdresse(Adresse $nouvelleAdresse): void
    {
        if ($this->adresse->equals($nouvelleAdresse)) {
            return;
        }

        $this->adresse = $nouvelleAdresse;
        $this->touch();
    }

    public function changerTelephone(string $numero): void
    {
        $nouveauTelephone = match ($this->adresse->pays) {
            Pays::France   => Telephone::francais($numero),
            Pays::Belgique => Telephone::belge($numero),
        };

        $this->telephone = $nouveauTelephone;
        $this->touch();
    }

    public function changerEmailContact(string $email): void
    {
        $nouvelEmail = new Email($email);

        if ($this->emailContact->equals($nouvelEmail)) {
            return;
        }

        $this->emailContact = $nouvelEmail;
        $this->touch();
    }

    public function ajouterSalle(SalleId $salleId): void
    {
        // Un cinéma peut toujours ajouter des salles (extensions de bâtiment)

        if ($this->possedeSalle($salleId)) {
            return; // Déjà présente
        }

        $this->salleIds[] = $salleId;
        $this->recordEvent(new SalleAjouteeEvent($this->id, $salleId));
        $this->touch();
    }

    public function supprimerSalle(SalleId $salleId): void
    {
        if (! $this->possedeSalle($salleId)) {
            return; // Pas présente
        }

        $this->salleIds = array_values(array_filter(
            $this->salleIds,
            fn (SalleId $id) => ! $id->equals($salleId)
        ));

        $this->touch();
    }

    public function changerStatut(StatusCinema $nouveauStatut): void
    {
        if ($this->statut === $nouveauStatut) {
            return;
        }

        $this->statut = $nouveauStatut;
        $this->touch();
    }

    public function changerCoordonneesGPS(CoordonneesGPS $nouvellescoordonneesGPS): void
    {
        if ($this->coordonneesGPS->equals($nouvellescoordonneesGPS)) {
            return;
        }

        $this->coordonneesGPS = $nouvellescoordonneesGPS;
        $this->touch();
    }

    public function rendreOperationnel(): void
    {
        if ($this->statut === StatusCinema::Actif) {
            return;
        }

        $this->changerStatut(StatusCinema::Actif);
    }

    public function rendreNonOperationnel(): void
    {
        if ($this->statut === StatusCinema::Ferme) {
            return;
        }

        $this->changerStatut(StatusCinema::Ferme);
    }

    /**
     * @param  array<string>  $horaires
     */
    public function ajouterHoraire(string $jour, array $horaires): void
    {
        /** @var array{monday?: array<string>, tuesday?: array<string>, wednesday?: array<string>, thursday?: array<string>, friday?: array<string>, saturday?: array<string>, sunday?: array<string>} $data */
        $data                    = [$jour => $horaires];
        $this->horairesOuverture = $this->mergeOpeningHoursData(
            $this->horairesOuverture,
            $data
        );
        $this->touch();
    }

    public function supprimerHoraire(string $jour): void
    {
        $this->horairesOuverture = $this->removeFromOpeningHours(
            $this->horairesOuverture,
            [$jour]
        );
        $this->touch();
    }

    /**
     * @param array{
     *     monday?: array<string>,
     *     tuesday?: array<string>,
     *     wednesday?: array<string>,
     *     thursday?: array<string>,
     *     friday?: array<string>,
     *     saturday?: array<string>,
     *     sunday?: array<string>,
     *     exceptions?: array<array<string>>,
     * } $horairesStandards
     */
    public function definirHoraire(array $horairesStandards): void
    {
        $existingData     = $this->extractOpeningHoursData($this->horairesOuverture);
        $nouveauxHoraires = array_merge($horairesStandards, [
            'exceptions' => $existingData['exceptions'] ?? [],
        ]);

        $this->horairesOuverture = OpeningHours::create($nouveauxHoraires);
        $this->touch();
    }

    /**
     * @param  array<string>  $horaires
     */
    public function ajouterSpecialHoraire(string $date, array $horaires): void
    {
        $existingData                      = $this->extractOpeningHoursData($this->horairesOuverture);
        $existingData['exceptions'] ??= [];
        $existingData['exceptions'][$date] = $horaires;

        $this->horairesOuverture = OpeningHours::create($existingData);
        $this->touch();
    }

    public function supprimerSpecialHoraire(string $date): void
    {
        $this->horairesOuverture = $this->removeFromOpeningHours(
            $this->horairesOuverture,
            [$date]
        );
        $this->touch();
    }

    /**
     * @param  array<string, array<string>>  $exceptions
     */
    public function definirSpecialHoraire(array $exceptions): void
    {
        $existingData               = $this->extractOpeningHoursData($this->horairesOuverture);
        $existingData['exceptions'] = $exceptions;

        $this->horairesOuverture = OpeningHours::create($existingData);
        $this->touch();
    }

    public function possedeSalle(SalleId $salleId): bool
    {
        foreach ($this->salleIds as $id) {
            if ($id->equals($salleId)) {
                return true;
            }
        }

        return false;
    }

    public function nombreDeSalles(): int
    {
        return count($this->salleIds);
    }

    public function possedeSalles(): bool
    {
        return $this->nombreDeSalles() > 0;
    }

    public function estOperationnel(): bool
    {
        return $this->statut === StatusCinema::Actif && $this->possedeSalles();
    }

    public function estOuvert(?CarbonImmutable $moment = null): bool
    {
        if (! $this->estOperationnel()) {
            return false;
        }

        return $this->horairesOuverture->isOpenAt($moment ?? CarbonImmutable::now());
    }

    public function estOuvertPourSeance(CarbonImmutable $heureSeance): bool
    {
        if (! $this->estOperationnel()) {
            return false;
        }

        // Vérifier ouverture 30min avant et 3h après la séance
        $debutPeriode = $heureSeance->subMinutes(30);
        $finPeriode   = $heureSeance->addHours(3);

        return $this->horairesOuverture->isOpenAt($debutPeriode) &&
            $this->horairesOuverture->isOpenAt($finPeriode);
    }

    public function prochaineOuverture(): ?CarbonImmutable
    {
        if (! $this->estOperationnel()) {
            return null;
        }

        return CarbonImmutable::instance($this->horairesOuverture->nextOpen());
    }

    public function prochaineFermeture(): ?CarbonImmutable
    {
        if (! $this->estOperationnel()) {
            return null;
        }

        return CarbonImmutable::instance($this->horairesOuverture->nextClose());
    }

    public function estDansLaVille(string $ville): bool
    {
        return mb_strtolower($this->adresse->ville) === mb_strtolower($ville);
    }

    public function estDansLePays(string $pays): bool
    {
        return mb_strtolower($this->adresse->pays
            ->label()) === mb_strtolower($pays);
    }

    public function equals(EntityInterface $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->id->equals($other->id);
    }

    private function enforceInvariants(): void
    {
        $this->validerCodeCinema($this->codeCinema);
        $this->validerNom($this->nom);
        $this->validerSalleIds($this->salleIds);
    }

    private function validerCodeCinema(string $codeCinema): void
    {
        try {
            v::stringType()->notEmpty()
                ->length(3, 4)
                ->regex('/^[A-Z0-9]{3,4}$/u')
                ->assert($codeCinema);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Code cinéma invalid: must be 3-4 uppercase letters or numbers');
        }
    }

    private function validerNom(string $nom): void
    {
        try {
            v::stringType()->notEmpty()
                ->length(3, 100)
                ->regex('/^[\p{L}\p{N}\s\'-\.]+$/u')
                ->assert($nom);
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Cinema name invalid: must be 3-100 characters with letters, numbers, spaces, quotes, hyphens and dots only');
        }
    }

    /**
     * @param  array<SalleId>  $salleIds
     *
     * @throws InvalidArgumentException
     */
    private function validerSalleIds(array $salleIds): void // @phpstan-ignore void.pure,throws.unusedType
    {
        // Validation que chaque élément est bien une instance de SalleId
        foreach ($salleIds as $salleId) {
            // @phpstan-ignore instanceof.alwaysTrue
            if (! ($salleId instanceof SalleId)) {
                throw new InvalidArgumentException('Invalid SalleId');
            }
        }
    }

    private function touch(): void
    {
        $this->updatedAt = CarbonImmutable::now();
    }
}
