<?php

declare(strict_types=1);

use App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasMapper;
use App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasRequest;
use App\Application\UseCases\Cinema\AfficherCinemas\AfficherCinemasResponse;
use App\Domain\Collections\CinemaCollection;
use App\Domain\Contracts\Repositories\Cinema\CinemaCriteria;
use CuyZ\Valinor\Mapper\MappingError;

describe('AfficherCinemasMapper', function () {
    beforeEach(function () {
        $this->mapper = new AfficherCinemasMapper();
    });

    it('maps HTTP data to Request with Valinor validation', function () {
        $data = [
            'recherche'    => 'cinema paris',
            'operationnel' => true,
            'pays'         => 'France',
            'ville'        => 'Paris',
            'page'         => 1,
            'perPage'      => 20,
        ];

        $request = $this->mapper->mapToRequest($data);

        expect($request)->toBeInstanceOf(AfficherCinemasRequest::class);
        expect($request->recherche)->toBe('cinema paris');
        expect($request->operationnel)->toBeTrue();
        expect($request->pays)->toBe('France');
        expect($request->ville)->toBe('Paris');
        expect($request->page)->toBe(1);
        expect($request->perPage)->toBe(20);
    });

    it('validates HTTP data with Valinor constraints', function () {
        try {
            $this->mapper->mapToRequest(['page' => 0]); // Invalid positive-int
            expect(false)->toBeTrue('Should have thrown MappingError for page=0');
        } catch (MappingError $e) {
            expect($e)->toBeInstanceOf(MappingError::class);
        }

        try {
            $this->mapper->mapToRequest(['perPage' => 101]); // Invalid range
            expect(false)->toBeTrue('Should have thrown MappingError for perPage=101');
        } catch (MappingError $e) {
            expect($e)->toBeInstanceOf(MappingError::class);
        }

        try {
            $this->mapper->mapToRequest(['recherche' => '']); // Invalid non-empty-string
            expect(false)->toBeTrue('Should have thrown MappingError for empty recherche');
        } catch (MappingError $e) {
            expect($e)->toBeInstanceOf(MappingError::class);
        }
    });

    it('maps Request to CinemaCriteria correctly', function () {
        $request = new AfficherCinemasRequest(
            recherche: 'test search',
            operationnel: false,
            pays: 'Belgium',
            ville: 'Brussels',
            page: 2,
            perPage: 25
        );

        $criteria = $this->mapper->mapToCriteria($request);

        expect($criteria)->toBeInstanceOf(CinemaCriteria::class);
        expect($criteria->recherche)->toBe('test search');
        expect($criteria->operationnel)->toBeFalse();
        expect($criteria->pays)->toBe('Belgium');
        expect($criteria->ville)->toBe('Brussels');
        expect($criteria->page)->toBe(2);
        expect($criteria->perPage)->toBe(25);
    });

    it('maps domain result to Response with pagination', function () {
        $cinemas  = new CinemaCollection();
        $criteria = new CinemaCriteria(page: 1, perPage: 10);

        $response = $this->mapper->mapToResponse($cinemas, $criteria);

        expect($response)->toBeInstanceOf(AfficherCinemasResponse::class);
        expect($response->cinemas)->toBe($cinemas);
        expect($response->criteria)->toBe($criteria);
        expect($response->pagination)->not->toBeNull();
        expect($response->pagination->total)->toBe(0);
    });

    it('handles empty HTTP data correctly', function () {
        $request = $this->mapper->mapToRequest([]);

        expect($request->recherche)->toBeNull();
        expect($request->operationnel)->toBeNull();
        expect($request->pays)->toBeNull();
        expect($request->ville)->toBeNull();
        expect($request->page)->toBeNull();
        expect($request->perPage)->toBeNull();
    });
});
