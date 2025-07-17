<?php

declare(strict_types=1);

use App\Domain\Enums\PaysAdresse;
use App\Domain\ValueObjects\Commun\Adresse;

describe('Adresse Value Object', function () {

    describe('Construction et Factory Methods', function () {
        it('crée une adresse française', function () {
            $adresse = Adresse::francaise('123 Rue de la Paix', '75001', 'Paris');

            expect($adresse->rue)->toBe('123 Rue de la Paix');
            expect($adresse->codePostal)->toBe('75001');
            expect($adresse->ville)->toBe('Paris');
            expect($adresse->pays)->toBe(PaysAdresse::France);
        });

        it('crée une adresse belge', function () {
            $adresse = Adresse::belge('Rue des Belges 42', '1000', 'Bruxelles');

            expect($adresse->rue)->toBe('Rue des Belges 42');
            expect($adresse->codePostal)->toBe('1000');
            expect($adresse->ville)->toBe('Bruxelles');
            expect($adresse->pays)->toBe(PaysAdresse::Belgique);
        });

        it('normalise les espaces multiples', function () {
            $adresse = Adresse::francaise('  123   Rue    de   la   Paix  ', '75001', '  Paris  ');

            expect($adresse->rue)->toBe('123 Rue de la Paix');
            expect($adresse->ville)->toBe('Paris');
        });

        it('normalise le code postal', function () {
            $adresse = Adresse::francaise('123 Rue de la Paix', '75-001', 'Paris');

            expect($adresse->codePostal)->toBe('75001');
        });
    });

    describe('Getters et Accesseurs', function () {
        it('expose toutes les propriétés', function () {
            $adresse = Adresse::francaise('123 Rue de la Paix', '75001', 'Paris');

            expect($adresse->rue)->toBe('123 Rue de la Paix');
            expect($adresse->codePostal)->toBe('75001');
            expect($adresse->ville)->toBe('Paris');
            expect($adresse->pays)->toBe(PaysAdresse::France);
        });
    });

    describe('Comparaisons', function () {
        it('détermine l\'égalité correctement', function () {
            $adresse1 = Adresse::francaise('123 Rue de la Paix', '75001', 'Paris');
            $adresse2 = Adresse::francaise('123 Rue de la Paix', '75001', 'Paris');
            $adresse3 = Adresse::francaise('456 Avenue des Champs', '75008', 'Paris');

            expect($adresse1->equals($adresse2))->toBeTrue();
            expect($adresse1->equals($adresse3))->toBeFalse();
        });

        it('considère les adresses différentes par pays', function () {
            $adresseFR = Adresse::francaise('123 Rue de la Paix', '75001', 'Paris');
            $adresseBE = Adresse::belge('123 Rue de la Paix', '1000', 'Bruxelles');

            expect($adresseFR->equals($adresseBE))->toBeFalse();
        });
    });

    describe('Mutations et Opérations', function () {
        it('modifie la rue', function () {
            $adresse         = Adresse::francaise('123 Rue de la Paix', '75001', 'Paris');
            $nouvelleAdresse = $adresse->withRue('456 Avenue des Champs');

            expect($nouvelleAdresse->rue)->toBe('456 Avenue des Champs');
            expect($nouvelleAdresse->codePostal)->toBe('75001');
            expect($nouvelleAdresse->ville)->toBe('Paris');
            expect($nouvelleAdresse->pays)->toBe(PaysAdresse::France);
        });

        it('modifie le code postal', function () {
            $adresse         = Adresse::francaise('123 Rue de la Paix', '75001', 'Paris');
            $nouvelleAdresse = $adresse->withCodePostal('75008');

            expect($nouvelleAdresse->rue)->toBe('123 Rue de la Paix');
            expect($nouvelleAdresse->codePostal)->toBe('75008');
            expect($nouvelleAdresse->ville)->toBe('Paris');
            expect($nouvelleAdresse->pays)->toBe(PaysAdresse::France);
        });

        it('modifie la ville', function () {
            $adresse         = Adresse::francaise('123 Rue de la Paix', '75001', 'Paris');
            $nouvelleAdresse = $adresse->withVille('Lyon');

            expect($nouvelleAdresse->rue)->toBe('123 Rue de la Paix');
            expect($nouvelleAdresse->codePostal)->toBe('75001');
            expect($nouvelleAdresse->ville)->toBe('Lyon');
            expect($nouvelleAdresse->pays)->toBe(PaysAdresse::France);
        });

        it('modifie le pays', function () {
            $adresse         = Adresse::francaise('123 Rue de la Paix', '75001', 'Paris');
            $nouvelleAdresse = $adresse->withPays(PaysAdresse::Belgique);

            expect($nouvelleAdresse->rue)->toBe('123 Rue de la Paix');
            expect($nouvelleAdresse->codePostal)->toBe('75001');
            expect($nouvelleAdresse->ville)->toBe('Paris');
            expect($nouvelleAdresse->pays)->toBe(PaysAdresse::Belgique);
        })->throws(InvalidArgumentException::class, 'Code postal belge invalide');
    });

    describe('Validation et Règles Métier', function () {
        it('valide les codes postaux français', function (string $codePostal) {
            $adresse = Adresse::francaise('123 Rue de la Paix', $codePostal, 'Paris');
            expect($adresse->codePostal)->toBe($codePostal);
        })->with([
            '01000', '13001', '33000', '59000', '75001', '99999',
        ]);

        it('valide les codes postaux belges', function (string $codePostal) {
            $adresse = Adresse::belge('Rue des Belges 42', $codePostal, 'Bruxelles');
            expect($adresse->codePostal)->toBe($codePostal);
        })->with([
            '1000', '2000', '4000', '6000', '8000', '9999',
        ]);

        it('rejette les codes postaux français invalides', function (string $codePostal) {
            expect(fn () => Adresse::francaise('123 Rue de la Paix', $codePostal, 'Paris'))
                ->toThrow(InvalidArgumentException::class);
        })->with([
            '00999', '100000', '1234', 'abcde',
        ]);

        it('rejette les codes postaux belges invalides', function (string $codePostal) {
            expect(fn () => Adresse::belge('Rue des Belges 42', $codePostal, 'Bruxelles'))
                ->toThrow(InvalidArgumentException::class);
        })->with([
            '999', '10000', '123', 'abcd',
        ]);

        it('rejette les rues invalides', function (string $rue) {
            expect(fn () => Adresse::francaise($rue, '75001', 'Paris'))
                ->toThrow(InvalidArgumentException::class);
        })->with([
            '', '  ', 'ab', str_repeat('a', 256),
        ]);

        it('rejette les villes invalides', function (string $ville) {
            expect(fn () => Adresse::francaise('123 Rue de la Paix', '75001', $ville))
                ->toThrow(InvalidArgumentException::class);
        })->with([
            '', '  ', 'a', 'Paris123', 'Paris@', str_repeat('a', 101),
        ]);

        it('accepte les villes avec apostrophes et tirets', function (string $ville) {
            $adresse = Adresse::francaise('123 Rue de la Paix', '75001', $ville);
            expect($adresse->ville)->toBe($ville);
        })->with([
            'Aix-en-Provence', 'L\'Haÿ-les-Roses', 'Saint-Étienne', 'Bourg-en-Bresse',
        ]);
    });

    describe('Immutabilité et Structure', function () {
        it('est immutable par design', function () {
            $classReflection = new ReflectionClass(Adresse::class);
            expect($classReflection->isReadOnly())->toBeTrue();
            expect($classReflection->isFinal())->toBeTrue();

            $properties = $classReflection->getProperties();
            foreach ($properties as $property) {
                expect($property->isReadOnly())->toBeTrue();
            }
        });

        it('retourne une nouvelle instance lors des mutations', function () {
            $adresse         = Adresse::francaise('123 Rue de la Paix', '75001', 'Paris');
            $nouvelleAdresse = $adresse->withRue('456 Avenue des Champs');

            expect($adresse)->not->toBe($nouvelleAdresse);
            expect($adresse->rue)->toBe('123 Rue de la Paix');
            expect($nouvelleAdresse->rue)->toBe('456 Avenue des Champs');
        });
    });
});
