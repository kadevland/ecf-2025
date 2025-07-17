<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewModels;

use App\Domain\Entities\Incident\Incident;

/**
 * ViewModel pour les incidents
 */
final readonly class IncidentViewModel
{
    public Incident $incident;

    public string $id;

    public string $titre;

    public string $description;

    public string $type;

    public string $typeValue;

    public string $typeIcon;

    public string $priorite;

    public string $prioriteValue;

    public string $prioriteBadge;

    public int $prioriteOrdre;

    public string $statut;

    public string $statutValue;

    public string $statutBadge;

    public string $dateCreation;

    public string $dateCreationIso;

    public ?string $dateResolution;

    public ?string $delaiResolution;

    public string $dureeOuverte;

    public string $localisation;

    public ?string $solutionApportee;

    public ?string $commentairesInternes;

    public bool $estOuvert;

    public bool $estEnCours;

    public bool $estResolu;

    public bool $estFerme;

    public bool $estActif;

    public bool $estAssigne;

    public string $rapporteParNom;

    public string $cinemaName;

    public ?string $salleNom;

    public ?string $assigneANom;

    public string $uuid;

    public string $createdAtShort;

    public string $updatedAt;

    public function __construct(Incident $incident)
    {
        $this->incident             = $incident;
        $this->id                   = $incident->id->uuid;
        $this->titre                = $incident->titre;
        $this->description          = $incident->description;
        $this->type                 = $incident->type->label();
        $this->typeValue            = $incident->type->value;
        $this->typeIcon             = $incident->type->icon();
        $this->priorite             = $incident->priorite->label();
        $this->prioriteValue        = $incident->priorite->value;
        $this->prioriteBadge        = $incident->priorite->badgeClass();
        $this->prioriteOrdre        = $incident->priorite->ordre();
        $this->statut               = $incident->statut->label();
        $this->statutValue          = $incident->statut->value;
        $this->statutBadge          = $incident->statut->badgeClass();
        $this->dateCreation         = $incident->createdAt->format('d/m/Y H:i');
        $this->dateCreationIso      = $incident->createdAt->toISOString();
        $this->dateResolution       = $incident->resolueAt?->format('d/m/Y H:i');
        $this->delaiResolution      = $this->calculateDelaiResolution($incident);
        $this->dureeOuverte         = $this->calculateDureeOuverte($incident);
        $this->localisation         = $this->getLocalisation($incident);
        $this->solutionApportee     = $incident->solutionApportee;
        $this->commentairesInternes = $incident->commentairesInternes;
        $this->estOuvert            = $incident->estOuvert();
        $this->estEnCours           = $incident->estEnCours();
        $this->estResolu            = $incident->estResolu();
        $this->estFerme             = $incident->estFerme();
        $this->estActif             = $incident->estActif;
        $this->estAssigne           = $incident->estAssigne();
        $this->rapporteParNom       = 'Employé '.mb_substr($incident->rapportePar->uuid, 0, 8);
        $this->cinemaName           = 'Cinéma '.mb_substr($incident->cinemaId->uuid, 0, 8);
        $this->salleNom             = $incident->salleId ? 'Salle '.mb_substr($incident->salleId->uuid, 0, 4) : null;
        $this->assigneANom          = $incident->assigneA ? 'Technicien '.mb_substr($incident->assigneA->uuid, 0, 8) : null;
        $this->uuid                 = $incident->id->uuid;
        $this->createdAtShort       = $incident->createdAt->format('d/m/Y');
        $this->updatedAt            = $incident->updatedAt->format('d/m/Y H:i');
    }

    private function calculateDelaiResolution(Incident $incident): ?string
    {
        if ($incident->resolueAt === null) {
            return null;
        }

        $delai = $incident->createdAt->diffInMinutes($incident->resolueAt);

        if ($delai < 60) {
            return $delai.' min';
        }

        $heures  = (int) ($delai / 60);
        $minutes = $delai % 60;

        return $heures.'h'.($minutes > 0 ? ' '.$minutes.'min' : '');
    }

    private function calculateDureeOuverte(Incident $incident): string
    {
        $fin   = $incident->resolueAt ?? \Carbon\CarbonImmutable::now();
        $duree = $incident->createdAt->diffInMinutes($fin);

        if ($duree < 60) {
            return $duree.' min';
        }

        $heures  = (int) ($duree / 60);
        $minutes = $duree % 60;

        return $heures.'h'.($minutes > 0 ? ' '.$minutes.'min' : '');
    }

    private function getLocalisation(Incident $incident): string
    {
        if ($incident->salleId) {
            return "Salle {$incident->salleId->uuid}";
        }

        return "Cinéma {$incident->cinemaId->uuid}";
    }
}
