<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages\Elements\Table;

use App\Application\Presenters\Html\ViewModels\ClientViewModel;
use App\Domain\Entities\User\Components\Profiles\ClientProfile;
use App\Domain\Entities\User\User;
use App\Domain\Enums\UserStatus;

/**
 * Élément de ligne pour un client
 */
final readonly class ClientItemListElement extends ItemListElement
{
    public string $classeBadgeStatut;

    public function __construct(
        private ClientViewModel $client,
        ActionListView $actions,
    ) {
        parent::__construct($actions);

        $this->classeBadgeStatut = $this->classeBadgeStatut();

    }

    /**
     * Créer un item client avec ses actions
     */
    public static function creer(User $client): self
    {
        $clientViewModel = new ClientViewModel($client);

        $actions = collect([
            new Action(
                label: 'Voir',
                url: '#', // TODO: créer la route show
                icon: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                class: 'btn-info',
            ),

            new Action(
                label: 'Modifier',
                url: '#', // TODO: créer la route edit
                icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                class: 'btn-warning',
            ),
        ]);

        $actionList = new ActionListView(
            actions: $actions,
            isDropdown: true,
            dropdownLabel: 'Actions',
        );

        return new self($clientViewModel, $actionList);
    }

    /**
     * Affiche la valeur pour une colonne donnée
     */
    public function displayValue(string $columnKey): mixed
    {

        if (property_exists($this, $columnKey)) {

            return $this->{$columnKey};
        }

        return property_exists($this->client, $columnKey) ? $this->client->{$columnKey} : '';
    }

    /**
     * Email du client
     */
    public function email(): string
    {
        return $this->client->email->value;
    }

    /**
     * Nom du client
     */
    public function nom(): string
    {
        /** @var ClientProfile $profile */
        $profile = $this->client->profile;

        return $profile->lastName;
    }

    /**
     * Prénom du client
     */
    public function prenom(): string
    {
        /** @var ClientProfile $profile */
        $profile = $this->client->profile;

        return $profile->firstName;
    }

    /**
     * Statut du client
     */
    public function status(): string
    {
        return match ($this->client->status) {
            UserStatus::Active              => 'Actif',
            UserStatus::Suspended           => 'Suspendu',
            UserStatus::PendingVerification => 'En attente',
        };
    }

    /**
     * Classe CSS pour le statut
     */
    public function statusClass(): string
    {
        return match ($this->client->status) {
            UserStatus::Active              => 'badge-success',
            UserStatus::Suspended           => 'badge-error',
            UserStatus::PendingVerification => 'badge-warning',
        };
    }

    /**
     * Date de création formatée
     */
    public function createdAt(): string
    {
        return $this->client->createdAt->format('d/m/Y H:i');
    }

    private function classeBadgeStatut(): string
    {

        return $this->client->statut === UserStatus::Active->value ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
    }

    /**
     * Actions disponibles
     */
    private function createActions(): ActionListView
    {
        return new ActionListView([
            Action::create('Voir', route('gestion.clients.show', $this->client->id->uuid), 'eye'),
            Action::create('Modifier', route('gestion.clients.edit', $this->client->id->uuid), 'edit'),
        ]);
    }
}
