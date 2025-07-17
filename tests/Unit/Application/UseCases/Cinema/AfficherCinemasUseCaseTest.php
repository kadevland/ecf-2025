<?php

declare(strict_types=1);

use App\Application\DTOs\PaginationInfo;
use App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasMapper;
use App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasRequest;
use App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasResponse;
use App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasUseCase;
use App\Domain\Collections\CinemaCollection;
use App\Domain\Collections\PaginatedCollection;
use App\Domain\Contracts\Repositories\Cinema\CinemaCriteria;
use App\Domain\Contracts\Repositories\Cinema\CinemaRepositoryInterface;

describe('AfficherCinemasUseCase', function () {
    beforeEach(function () {
        $this->repository = Mockery::mock(CinemaRepositoryInterface::class);
        $this->mapper     = new AfficherCinemasMapper();
        $this->useCase    = new AfficherCinemasUseCase($this->repository, $this->mapper);
        $this->cinemas    = new CinemaCollection();
    });

    afterEach(function () {
        Mockery::close();
    });

    it('executes with typed request using pagination', function () {
        $request = new AfficherCinemasRequest(
            recherche: 'Paris',
            operationnel: true,
            page: 1,
            perPage: 10
        );

        $pagination      = PaginationInfo::fromPageParams(0, 1, 10);
        $paginatedResult = PaginatedCollection::create($this->cinemas, $pagination);

        $this->repository
            ->shouldReceive('findPaginatedByCriteria')
            ->once()
            ->with(Mockery::type(CinemaCriteria::class))
            ->andReturn($paginatedResult);

        $response = $this->useCase->execute($request);

        expect($response)->toBeInstanceOf(AfficherCinemasResponse::class);
        expect($response->cinemas)->toBe($this->cinemas);
        expect($response->criteria)->toBeInstanceOf(CinemaCriteria::class);
        expect($response->criteria->recherche)->toBe('Paris');
        expect($response->criteria->operationnel)->toBeTrue();
    });

    it('executes without pagination when page/perPage are null', function () {
        $request = new AfficherCinemasRequest(
            recherche: 'Paris',
            operationnel: true,
            page: null,
            perPage: null
        );

        $this->repository
            ->shouldReceive('findByCriteria')
            ->once()
            ->with(Mockery::type(CinemaCriteria::class))
            ->andReturn($this->cinemas);

        $response = $this->useCase->execute($request);

        expect($response)->toBeInstanceOf(AfficherCinemasResponse::class);
        expect($response->cinemas)->toBe($this->cinemas);
    });

    it('creates pagination info correctly', function () {
        $request = new AfficherCinemasRequest(page: 1, perPage: 5);

        $pagination      = PaginationInfo::fromPageParams(0, 1, 5);
        $paginatedResult = PaginatedCollection::create($this->cinemas, $pagination);

        $this->repository
            ->shouldReceive('findPaginatedByCriteria')
            ->once()
            ->andReturn($paginatedResult);

        $response = $this->useCase->execute($request);

        expect($response->pagination)->not->toBeNull();
        expect($response->pagination->total)->toBe(0); // Empty collection
    });
});
