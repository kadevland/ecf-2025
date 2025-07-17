<?php

declare(strict_types=1);

namespace App\Application\Mappers\Http;

use App\Application\DTOs\Cinema\CreerCinemaDto;
use App\Application\DTOs\Cinema\ModifierCinemaDto;
use App\Domain\Enums\Pays;
use App\Domain\ValueObjects\Commun\Adresse;
use App\Domain\ValueObjects\Commun\Telephone;
use Illuminate\Http\Request;
use Spatie\OpeningHours\OpeningHours;

final readonly class CinemaHttpMapper
{
    public function __construct()
    {
        // Simplified mapper without Valinor for now
    }

    public function toAfficherCinemasRequest(Request $request): \App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasRequest
    {
        return new \App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasRequest(
            search: $request->get('search'),
            operationnel: $request->has('operationnel') ? $request->boolean('operationnel') : null,
            pays: $request->get('pays'),
            ville: $request->get('ville'),
            limit: $request->get('limit', 20),
            offset: $request->get('offset', 0)
        );
    }

    public function mapToCreerCinemaDto(Request $request): CreerCinemaDto
    {
        $data = $request->validated();

        // Simple mapping without Valinor for now
        return new CreerCinemaDto(
            nom: $data['nom'],
            adresse: $this->mapAdresse($data['adresse']),
            telephone: $this->mapTelephone($data['telephone']),
            emailContact: $data['email_contact'],
            horairesOuverture: $this->mapHorairesOuverture($data['horaires_ouverture'] ?? []),
            services: $data['services']       ?? [],
            description: $data['description'] ?? null,
            coordonneesGps: $this->mapCoordonneesGps($data['coordonnees_gps'] ?? null),
        );
    }

    public function mapToModifierCinemaDto(Request $request, string $cinemaId): ModifierCinemaDto
    {
        $data = $request->validated();

        // Simple mapping without Valinor for now
        return new ModifierCinemaDto(
            cinemaId: $cinemaId,
            nom: $data['nom'] ?? null,
            adresse: isset($data['adresse']) ? $this->mapAdresse($data['adresse']) : null,
            telephone: isset($data['telephone']) ? $this->mapTelephone($data['telephone']) : null,
            emailContact: $data['email_contact'] ?? null,
            horairesOuverture: isset($data['horaires_ouverture']) ? $this->mapHorairesOuverture($data['horaires_ouverture']) : null,
            services: $data['services']       ?? null,
            description: $data['description'] ?? null,
            coordonneesGps: isset($data['coordonnees_gps']) ? $this->mapCoordonneesGps($data['coordonnees_gps']) : null,
            estOperationnel: $data['est_operationnel'] ?? null,
        );
    }

    private function mapAdresse(array $adresseData): Adresse
    {
        $pays = Pays::tryFrom($adresseData['pays'] ?? 'FR') ?? Pays::France;

        return new Adresse(
            rue: $adresseData['rue'],
            codePostal: $adresseData['code_postal'],
            ville: $adresseData['ville'],
            pays: $pays
        );
    }

    private function mapTelephone(string $telephone): Telephone
    {
        // Détection automatique du pays selon le format
        if (str_starts_with($telephone, '+32') || str_starts_with($telephone, '0032')) {
            return Telephone::belge($telephone);
        }

        // Par défaut, français
        return Telephone::francais($telephone);
    }

    private function mapHorairesOuverture(array $horairesData): OpeningHours
    {
        // Convertir le format HTTP vers le format OpeningHours
        $openingHours = [];

        foreach ($horairesData as $jour => $horaires) {
            if (empty($horaires)) {
                $openingHours[$jour] = [];

                continue;
            }

            $openingHours[$jour] = is_array($horaires) ? $horaires : [$horaires];
        }

        return OpeningHours::create($openingHours);
    }

    private function mapCoordonneesGps(?array $coordonnees): ?array
    {
        if (empty($coordonnees)) {
            return null;
        }

        return [
            'latitude'  => (float) $coordonnees['latitude'],
            'longitude' => (float) $coordonnees['longitude'],
        ];
    }
}
