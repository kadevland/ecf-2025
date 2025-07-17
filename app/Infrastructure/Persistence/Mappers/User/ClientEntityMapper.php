<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers\User;

use App\Domain\Entities\EntityInterface;
use App\Domain\Entities\User\Components\Profiles\ClientProfile;
use App\Domain\Entities\User\User;
use App\Domain\Enums\UserType;
use App\Domain\ValueObjects\Commun\Email;
use App\Domain\ValueObjects\User\UserId;
use App\Infrastructure\Persistence\Mappers\AbstractEloquentMapper;
use App\Models\User as UserModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Log;
use Throwable;

final class ClientEntityMapper extends AbstractEloquentMapper
{
    public function toDomainEntity(Model $model): ?EntityInterface
    {
        try {
            /** @var UserModel $model */

            // Vérifier que c'est bien un client
            if ($model->user_type !== UserType::Client) {
                throw new InvalidArgumentException("User {$model->id} is not a client");
            }

            // Charger les relations manquantes avec loadMissing
            $model->loadMissing([
                'profile:id,uuid,first_name,last_name,phone',
            ]);

            // Charger le profil client
            $clientProfile = $model->profile;
            if (! $clientProfile) {
                throw new InvalidArgumentException("Client profile not found for user {$model->id}");
            }

            // Créer le profil client
            $profile = ClientProfile::create(
                firstName: $clientProfile->first_name,
                lastName: $clientProfile->last_name,
                phone: $clientProfile->phone
            );

            // Créer l'utilisateur directement avec le constructeur
            return new User(
                id: UserId::fromDatabase((int) $model->id, $model->uuid),
                email: Email::fromString($model->email),
                userType: UserType::Client,
                statut: $model->status,
                profile: $profile,
                emailVerifiedAt: $model->email_verified_at ? CarbonImmutable::parse($model->email_verified_at) : null,
                createdAt: CarbonImmutable::parse($model->created_at),
                updatedAt: CarbonImmutable::parse($model->updated_at)
            );
        } catch (Throwable $th) {
            // Log l'erreur pour debug mais ne casse pas l'application
            Log::error('Erreur mapping Client', ['model_id' => $model->id ?? 'unknown', 'error' => $th->getMessage()]);

            return null;
        }
    }

    public function toEloquentModel(EntityInterface $entity): Model
    {
        /** @var User $entity */

        // Vérifier que c'est bien un client
        if (! $entity->estClient()) {
            throw new InvalidArgumentException('Entity is not a client');
        }

        $model = new UserModel();

        $model->uuid              = $entity->id->uuid;
        $model->email             = $entity->email->value;
        $model->user_type         = UserType::Client;
        $model->status            = $entity->statut->value;
        $model->email_verified_at = $entity->emailVerifiedAt?->toDateTime();
        $model->profile_id        = null; // Sera défini après création du profil
        $model->created_at        = $entity->createdAt->toDateTime();
        $model->updated_at        = $entity->updatedAt->toDateTime();

        return $model;
    }

    /**
     * Crée ou met à jour le profil client associé
     */
    public function syncClientProfile(User $user, UserModel $userModel): void
    {
        if (! $user->estClient()) {
            throw new InvalidArgumentException('User is not a client');
        }

        /** @var ClientProfile $profile */
        $profile = $user->profile;

        // Trouver ou créer le profil client
        $clientModel = $userModel->profile ?: new \App\Models\Client();

        $clientModel->first_name = $profile->firstName;
        $clientModel->last_name  = $profile->lastName;
        $clientModel->phone      = $profile->phone();
        $clientModel->save();

        // Associer le profil à l'utilisateur
        $userModel->profile_id = $clientModel->id;
        $userModel->save();
    }
}
