<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewModels;

use App\Domain\Entities\Cinema\Cinema;

final readonly class CinemaViewModel
{
    public const DATE_FORMAT = 'd/m/Y';

    public Cinema $cinema;

    public string $id;

    public string $nom;

    public string $description;

    public int $nombreSalles;

    public bool $estActif;

    public string $statut;

    public string $classeBadgeStatut;

    public string $adresseComplete;

    public string $ville;

    public string $codePostal;

    public string $pays;

    public string $telephone;

    public string $email;

    public string $date;

    public array $coordonneesGPS;

    public string $horairesFormates;

    public bool $estOuvertAujourdhui;

    public string $horairesDuJour;

    public string $code;

    public function __construct(Cinema $cinema)
    {
        $this->cinema              = $cinema;
        $this->id                  = $cinema->id->uuid;
        $this->nom                 = $cinema->nom;
        $this->description         = 'Cinéma moderne avec équipements dernière génération';
        $this->nombreSalles        = $this->formatNombreSalles($cinema);
        $this->estActif            = $cinema->estOperationnel;
        $this->statut              = \Illuminate\Support\Str::ucfirst($cinema->statut->label());
        $this->classeBadgeStatut   = $this->formatClasseBadgeStatut($cinema);
        $this->adresseComplete     = $this->formatAdresseComplete($cinema);
        $this->ville               = $cinema->adresse->ville;
        $this->codePostal          = $cinema->adresse->codePostal;
        $this->pays                = $cinema->adresse->pays->label();
        $this->telephone           = $cinema->telephone->numero;
        $this->email               = $cinema->emailContact->value;
        $this->date                = $cinema->createdAt->format(self::DATE_FORMAT);
        $this->coordonneesGPS      = $this->formatCoordonneesGPS($cinema);
        $this->horairesFormates    = $this->formatHoraires($cinema);
        $this->estOuvertAujourdhui = $this->calculerOuvertureAujourdhui($cinema);
        $this->horairesDuJour      = $this->calculerHorairesDuJour($cinema);
        $this->code                = $cinema->codeCinema;
    }

    private function formatNombreSalles(Cinema $cinema): int
    {
        return count($cinema->salleIds);
    }

    private function formatClasseBadgeStatut(Cinema $cinema): string
    {
        return $cinema->estOperationnel ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    }

    private function formatCoordonneesGPS(Cinema $cinema): array
    {
        return [
            'latitude'  => $cinema->coordonneesGPS->latitude,
            'longitude' => $cinema->coordonneesGPS->longitude,
        ];
    }

    private function formatAdresseComplete(Cinema $cinema): string
    {
        return sprintf(
            '%s, %s %s %s',
            $cinema->adresse->rue,
            $cinema->adresse->codePostal,
            $cinema->adresse->ville,
            $cinema->adresse->pays->label()
        );
    }

    private function formatHoraires(Cinema $cinema): string
    {
        $horaires = $this->getHorairesData($cinema);
        if (empty($horaires)) {
            return 'Horaires non disponibles';
        }

        $result = [];
        foreach ($horaires as $jour => $heures) {
            if (! empty($heures['ouverture']) && ! empty($heures['fermeture'])) {
                $result[] = sprintf('%s: %s - %s', ucfirst($jour), $heures['ouverture'], $heures['fermeture']);
            }
        }

        return implode(', ', $result);
    }

    private function calculerOuvertureAujourdhui(Cinema $cinema): bool
    {
        if (! $cinema->estOperationnel) {
            return false;
        }

        $horaires = $this->getHorairesData($cinema);
        if (empty($horaires)) {
            return false;
        }

        $jourSemaine     = mb_strtolower(date('l'));
        $joursEnFrancais = [
            'monday'    => 'lundi',
            'tuesday'   => 'mardi',
            'wednesday' => 'mercredi',
            'thursday'  => 'jeudi',
            'friday'    => 'vendredi',
            'saturday'  => 'samedi',
            'sunday'    => 'dimanche',
        ];

        $jour = $joursEnFrancais[$jourSemaine] ?? null;
        if (! $jour) {
            return false;
        }

        $horairesDuJour = $horaires[$jour] ?? null;

        return $horairesDuJour && ! empty($horairesDuJour['ouverture']) && ! empty($horairesDuJour['fermeture']);
    }

    private function calculerHorairesDuJour(Cinema $cinema): string
    {
        if (! $this->calculerOuvertureAujourdhui($cinema)) {
            return 'Fermé';
        }

        $horaires        = $this->getHorairesData($cinema);
        $jourSemaine     = mb_strtolower(date('l'));
        $joursEnFrancais = [
            'monday'    => 'lundi',
            'tuesday'   => 'mardi',
            'wednesday' => 'mercredi',
            'thursday'  => 'jeudi',
            'friday'    => 'vendredi',
            'saturday'  => 'samedi',
            'sunday'    => 'dimanche',
        ];

        $jour = $joursEnFrancais[$jourSemaine] ?? null;
        if (! $jour) {
            return 'Fermé';
        }

        $horairesDuJour = $horaires[$jour] ?? null;
        if (! $horairesDuJour) {
            return 'Fermé';
        }

        return sprintf('%s - %s', $horairesDuJour['ouverture'], $horairesDuJour['fermeture']);
    }

    private function getHorairesData(Cinema $cinema): array
    {
        $horaires = $cinema->horairesOuverture;

        // Si horairesOuverture a une méthode pour extraire les données
        if (method_exists($horaires, 'horaires')) {
            return $horaires->horaires();
        }

        // Sinon retourner un tableau vide
        return [];
    }
}
