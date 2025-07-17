<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Conditions\Query\ConditionPagination;
use App\Domain\Collections\User\ClientCollection;
use App\Domain\Contracts\Repositories\User\ClientCriteria;
use App\Domain\Contracts\Repositories\User\ClientRepositoryInterface;
use App\Domain\Entities\User\User;
use App\Domain\Enums\UserType;
use App\Domain\ValueObjects\Commun\Email;
use App\Domain\ValueObjects\User\UserId;
use App\Infrastructure\Persistence\Applicators\PostgreSQLConditionApplicator;
use App\Infrastructure\Persistence\Mappers\User\ClientCriteriaToConditionsMapper;
use App\Infrastructure\Persistence\Mappers\User\ClientEntityMapper;
use App\Models\User as UserModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class EloquentClientRepository extends EloquentRepository implements ClientRepositoryInterface
{
    public function __construct(
        UserModel $model,
        ClientEntityMapper $entityMapper,
        private readonly ClientCriteriaToConditionsMapper $criteriaMapper,
        private readonly PostgreSQLConditionApplicator $conditionApplicator
    ) {
        parent::__construct($model, $entityMapper);
    }

    public function findByCriteria(ClientCriteria $criteria): ClientCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        $query      = $this->conditionApplicator->apply($this->builder(), $conditions);

        /** @var \Illuminate\Database\Eloquent\Collection<int, UserModel> $users */
        $users = $query->get();

        $collection = new ClientCollection();
        $users->each(function (UserModel $model) use ($collection): void {
            /** @var User $user */
            if ($user = $this->toEntity($model)) {
                $collection->add($user);
            }
        });

        return $collection;
    }

    public function findPaginatedByCriteria(ClientCriteria $criteria): \App\Domain\Collections\PaginatedCollection
    {
        $conditions = $this->criteriaMapper->map($criteria);
        // Exclure ConditionPagination car on gère la pagination avec Eloquent
        $query = $this->conditionApplicator->apply($this->builder(), $conditions, [ConditionPagination::class]);

        // Utiliser fastPaginate() pour performance
        $perPage   = $criteria->perPage ?? 15;
        $page      = $criteria->page    ?? 1;
        $paginator = $query->fastPaginate(perPage: $perPage, page: $page);

        // Mapper vers Collection domain
        $collection = new ClientCollection();
        $paginator->each(function (UserModel $model) use ($collection): void {
            /** @var User $user */
            if ($user = $this->toEntity($model)) {
                $collection->add($user);
            }
        });

        // Créer PaginationInfo depuis Eloquent Paginator
        $pagination = \App\Application\DTOs\PaginationInfo::fromPageParams(
            total: $paginator->total(),
            page: $paginator->currentPage(),
            perPage: $paginator->perPage()
        );

        return \App\Domain\Collections\PaginatedCollection::create($collection, $pagination);
    }

    public function findById(UserId $id): ?User
    {
        $model = $this->builder()
            ->where('uuid', $id->uuid)
            ->first();

        if (! $model) {
            return null;
        }

        /** @var User $user */
        $user = $this->toEntity($model);

        if (! $user->estClient()) {
            throw new InvalidArgumentException("User {$id->uuid} is not a client");
        }

        return $user;
    }

    public function findByEmail(Email $email): ?User
    {
        $model = $this->builder()
            ->where('email', $email->value)
            ->first();

        if (! $model) {
            return null;
        }

        /** @var User $user */
        $user = $this->toEntity($model);

        if (! $user->estClient()) {
            throw new InvalidArgumentException("User with email {$email->value} is not a client");
        }

        return $user;
    }

    public function save(User $client): User
    {
        if (! $client->estClient()) {
            throw new InvalidArgumentException('Entity is not a client');
        }

        return DB::transaction(function () use ($client): User {
            /** @var UserModel $model */
            $model = $client->id->isNew()
                ? $this->toModel($client)
                : $this->updateExistingModel($client);

            $model->save();

            // Synchroniser le profil client
            /** @var ClientEntityMapper $mapper */
            $mapper = $this->entityMapper;
            $mapper->syncClientProfile($client, $model);

            /** @var User */
            return $this->toEntity($model);
        });
    }

    protected function builder(): Builder
    {
        return parent::builder()
            ->with(['profile:id,uuid,first_name,last_name,phone'])
            ->where('users.user_type', UserType::Client->value);
    }

    private function updateExistingModel(User $client): UserModel
    {
        /** @var UserModel $model */
        $model = UserModel::where('uuid', $client->id->uuid)->first()
            ?? $this->toModel($client);

        // Update model properties from entity
        $updatedModel = $this->toModel($client);
        $model->fill($updatedModel->getAttributes());

        return $model;
    }
}
