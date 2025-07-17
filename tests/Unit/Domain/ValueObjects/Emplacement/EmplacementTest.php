<?php

declare(strict_types=1);

use App\Domain\Enums\CategorieSiege;
use App\Domain\Enums\EtatEmplacement;
use App\Domain\Enums\TypeEmplacement;
use App\Domain\ValueObjects\Emplacement\Emplacement;
use App\Domain\ValueObjects\Emplacement\NumeroEmplacement;

describe('Emplacement Value Object', function () {

    describe('Construction et Factory Methods', function () {
        it('crée un siège avec tous les paramètres', function () {
            $numero      = NumeroEmplacement::fromString('A12');
            $emplacement = Emplacement::siege($numero, 1, 12, CategorieSiege::Standard);

            expect($emplacement->numero())->toBe($numero);
            expect($emplacement->ligne())->toBe(1);
            expect($emplacement->colonne())->toBe(12);
            expect($emplacement->type())->toBe(TypeEmplacement::Siege);
            expect($emplacement->etat())->toBe(EtatEmplacement::Disponible);
            expect($emplacement->categorie())->toBe(CategorieSiege::Standard);
        });

        it('crée un siège avec état personnalisé', function () {
            $numero      = NumeroEmplacement::fromString('B5');
            $emplacement = Emplacement::siege($numero, 2, 5, CategorieSiege::PMR, EtatEmplacement::HorsService);

            expect($emplacement->etat())->toBe(EtatEmplacement::HorsService);
        });

        it('crée un emplacement vide', function () {
            $numero      = NumeroEmplacement::fromString('C1');
            $emplacement = Emplacement::vide($numero, 3, 1);

            expect($emplacement->numero())->toBe($numero);
            expect($emplacement->ligne())->toBe(3);
            expect($emplacement->colonne())->toBe(1);
            expect($emplacement->type())->toBe(TypeEmplacement::Vide);
            expect($emplacement->etat())->toBe(EtatEmplacement::Indisponible);
            expect($emplacement->categorie())->toBeNull();
        });
    });

    describe('Getters et Accesseurs', function () {
        it('expose toutes les propriétés d\'un siège', function () {
            $numero      = NumeroEmplacement::fromString('A12');
            $emplacement = Emplacement::siege($numero, 1, 12, CategorieSiege::Standard);

            expect($emplacement->numero())->toBe($numero);
            expect($emplacement->ligne())->toBe(1);
            expect($emplacement->colonne())->toBe(12);
            expect($emplacement->type())->toBe(TypeEmplacement::Siege);
            expect($emplacement->etat())->toBe(EtatEmplacement::Disponible);
            expect($emplacement->categorie())->toBe(CategorieSiege::Standard);
        });

        it('expose toutes les propriétés d\'un emplacement vide', function () {
            $numero      = NumeroEmplacement::fromString('C1');
            $emplacement = Emplacement::vide($numero, 3, 1);

            expect($emplacement->numero())->toBe($numero);
            expect($emplacement->ligne())->toBe(3);
            expect($emplacement->colonne())->toBe(1);
            expect($emplacement->type())->toBe(TypeEmplacement::Vide);
            expect($emplacement->etat())->toBe(EtatEmplacement::Indisponible);
            expect($emplacement->categorie())->toBeNull();
        });
    });

    describe('Prédicats et États', function () {
        it('détermine si c\'est un siège', function () {
            $siege = Emplacement::siege(NumeroEmplacement::fromString('A12'), 1, 12, CategorieSiege::Standard);
            $vide  = Emplacement::vide(NumeroEmplacement::fromString('C1'), 3, 1);

            expect($siege->estSiege())->toBeTrue();
            expect($vide->estSiege())->toBeFalse();
        });

        it('détermine si c\'est réservable', function () {
            $disponible  = Emplacement::siege(NumeroEmplacement::fromString('A12'), 1, 12, CategorieSiege::Standard);
            $reserve     = Emplacement::siege(NumeroEmplacement::fromString('B5'), 2, 5, CategorieSiege::PMR, EtatEmplacement::Reserve);
            $horsService = Emplacement::siege(NumeroEmplacement::fromString('C8'), 3, 8, CategorieSiege::Standard, EtatEmplacement::HorsService);
            $vide        = Emplacement::vide(NumeroEmplacement::fromString('D1'), 4, 1);

            expect($disponible->estReservable())->toBeTrue();
            expect($reserve->estReservable())->toBeFalse();
            expect($horsService->estReservable())->toBeFalse();
            expect($vide->estReservable())->toBeFalse();
        });
    });

    describe('Comparaisons', function () {
        it('détermine l\'égalité correctement', function () {
            $numero       = NumeroEmplacement::fromString('A12');
            $emplacement1 = Emplacement::siege($numero, 1, 12, CategorieSiege::Standard);
            $emplacement2 = Emplacement::siege($numero, 1, 12, CategorieSiege::Standard);
            $emplacement3 = Emplacement::siege($numero, 1, 12, CategorieSiege::PMR);

            expect($emplacement1->equals($emplacement2))->toBeTrue();
            expect($emplacement1->equals($emplacement3))->toBeFalse();
        });
    });

    describe('Mutations et Opérations', function () {
        it('change l\'état directement', function () {
            $emplacement = Emplacement::siege(NumeroEmplacement::fromString('A12'), 1, 12, CategorieSiege::Standard);
            $nouveauEtat = $emplacement->changerEtat(EtatEmplacement::HorsService);

            expect($nouveauEtat->etat())->toBe(EtatEmplacement::HorsService);
            expect($emplacement->etat())->toBe(EtatEmplacement::Disponible);
        });

        it('marque un siège comme réservé', function () {
            $emplacement = Emplacement::siege(NumeroEmplacement::fromString('A12'), 1, 12, CategorieSiege::Standard);
            $reserve     = $emplacement->marquerReserve();

            expect($reserve->etat())->toBe(EtatEmplacement::Reserve);
        });

        it('libère un siège réservé', function () {
            $emplacement = Emplacement::siege(NumeroEmplacement::fromString('A12'), 1, 12, CategorieSiege::Standard, EtatEmplacement::Reserve);
            $libere      = $emplacement->liberer();

            expect($libere->etat())->toBe(EtatEmplacement::Disponible);
        });

        it('marque un siège hors service', function () {
            $emplacement = Emplacement::siege(NumeroEmplacement::fromString('A12'), 1, 12, CategorieSiege::Standard);
            $horsService = $emplacement->marquerHorsService();

            expect($horsService->etat())->toBe(EtatEmplacement::HorsService);
        });

        it('rejette la réservation d\'un siège non réservable', function () {
            $horsService = Emplacement::siege(NumeroEmplacement::fromString('A12'), 1, 12, CategorieSiege::Standard, EtatEmplacement::HorsService);
            $vide        = Emplacement::vide(NumeroEmplacement::fromString('C1'), 3, 1);

            expect(fn () => $horsService->marquerReserve())
                ->toThrow(InvalidArgumentException::class, 'Cet emplacement n\'est pas réservable');

            expect(fn () => $vide->marquerReserve())
                ->toThrow(InvalidArgumentException::class, 'Cet emplacement n\'est pas réservable');
        });

        it('rejette la libération d\'un emplacement vide', function () {
            $vide = Emplacement::vide(NumeroEmplacement::fromString('C1'), 3, 1);

            expect(fn () => $vide->liberer())
                ->toThrow(InvalidArgumentException::class, 'Impossible de libérer un emplacement qui n\'est pas un siège');
        });

        it('rejette la mise hors service d\'un emplacement vide', function () {
            $vide = Emplacement::vide(NumeroEmplacement::fromString('C1'), 3, 1);

            expect(fn () => $vide->marquerHorsService())
                ->toThrow(InvalidArgumentException::class, 'Seuls les sièges peuvent être mis hors service');
        });
    });

    describe('Validation et Règles Métier', function () {
        it('valide les positions valides', function (int $ligne, int $colonne) {
            $emplacement = Emplacement::siege(NumeroEmplacement::fromString('A12'), $ligne, $colonne, CategorieSiege::Standard);

            expect($emplacement->ligne())->toBe($ligne);
            expect($emplacement->colonne())->toBe($colonne);
        })->with([
            [1, 1],
            [25, 50],
            [50, 100],
        ]);

        it('rejette les positions invalides', function (int $ligne, int $colonne) {
            expect(fn () => Emplacement::siege(NumeroEmplacement::fromString('A12'), $ligne, $colonne, CategorieSiege::Standard))
                ->toThrow(InvalidArgumentException::class);
        })->with([
            [0, 1],     // ligne trop petite
            [51, 1],    // ligne trop grande
            [1, 0],     // colonne trop petite
            [1, 101],   // colonne trop grande
        ]);

        it('valide que les sièges ont une catégorie', function () {
            $emplacement = Emplacement::siege(NumeroEmplacement::fromString('A12'), 1, 12, CategorieSiege::Standard);

            expect($emplacement->categorie())->toBe(CategorieSiege::Standard);
        });

        it('valide que les emplacements vides n\'ont pas de catégorie', function () {
            $emplacement = Emplacement::vide(NumeroEmplacement::fromString('C1'), 3, 1);

            expect($emplacement->categorie())->toBeNull();
        });

        it('valide les états cohérents pour les sièges', function (EtatEmplacement $etat) {
            $emplacement = Emplacement::siege(NumeroEmplacement::fromString('A12'), 1, 12, CategorieSiege::Standard, $etat);

            expect($emplacement->etat())->toBe($etat);
        })->with([
            EtatEmplacement::Disponible,
            EtatEmplacement::Reserve,
            EtatEmplacement::HorsService,
        ]);

        it('valide que les emplacements vides sont indisponibles', function () {
            $emplacement = Emplacement::vide(NumeroEmplacement::fromString('C1'), 3, 1);

            expect($emplacement->etat())->toBe(EtatEmplacement::Indisponible);
        });
    });

    describe('Immutabilité et Structure', function () {
        it('est immutable par design', function () {
            $classReflection = new ReflectionClass(Emplacement::class);
            expect($classReflection->isReadOnly())->toBeTrue();
            expect($classReflection->isFinal())->toBeTrue();

            $properties = $classReflection->getProperties();
            foreach ($properties as $property) {
                expect($property->isReadOnly())->toBeTrue();
            }
        });

        it('retourne une nouvelle instance lors des mutations', function () {
            $emplacement = Emplacement::siege(NumeroEmplacement::fromString('A12'), 1, 12, CategorieSiege::Standard);
            $reserve     = $emplacement->marquerReserve();

            expect($emplacement)->not->toBe($reserve);
            expect($emplacement->etat())->toBe(EtatEmplacement::Disponible);
            expect($reserve->etat())->toBe(EtatEmplacement::Reserve);
        });
    });
});
