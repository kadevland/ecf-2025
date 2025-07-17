<?php

declare(strict_types=1);

use App\Domain\ValueObjects\Emplacement\NumeroEmplacement;

describe('NumeroEmplacement Value Object', function () {

    describe('Construction et Factory Methods', function () {
        it('crée un numéro depuis une chaîne', function () {
            $numero = NumeroEmplacement::fromString('A12');

            expect($numero->valeur())->toBe('A12');
        });

        it('normalise les majuscules', function () {
            $numero = NumeroEmplacement::fromString('a12');

            expect($numero->valeur())->toBe('A12');
        });

        it('supprime les espaces', function () {
            $numero = NumeroEmplacement::fromString('  A12  ');

            expect($numero->valeur())->toBe('A12');
        });

        it('ajoute un zéro de padding pour les numéros à un chiffre', function () {
            $numero = NumeroEmplacement::fromString('A5');

            expect($numero->valeur())->toBe('A05');
        });

        it('préserve les numéros à deux chiffres', function () {
            $numero = NumeroEmplacement::fromString('A12');

            expect($numero->valeur())->toBe('A12');
        });

        it('préserve les numéros alphanumériques complexes', function () {
            $numero = NumeroEmplacement::fromString('VIP123');

            expect($numero->valeur())->toBe('VIP123');
        });
    });

    describe('Getters et Accesseurs', function () {
        it('retourne la valeur normalisée', function () {
            $numero = NumeroEmplacement::fromString('A12');

            expect($numero->valeur())->toBe('A12');
        });
    });

    describe('Comparaisons', function () {
        it('détermine l\'égalité correctement', function () {
            $numero1 = NumeroEmplacement::fromString('A12');
            $numero2 = NumeroEmplacement::fromString('a12'); // Normalisé vers A12
            $numero3 = NumeroEmplacement::fromString('B12');

            expect($numero1->equals($numero2))->toBeTrue();
            expect($numero1->equals($numero3))->toBeFalse();
        });

        it('gère l\'égalité avec padding', function () {
            $numero1 = NumeroEmplacement::fromString('A5');  // Normalisé vers A05
            $numero2 = NumeroEmplacement::fromString('A05');
            $numero3 = NumeroEmplacement::fromString('A50');

            expect($numero1->equals($numero2))->toBeTrue();
            expect($numero1->equals($numero3))->toBeFalse();
        });
    });

    describe('Validation et Règles Métier', function () {
        it('accepte les formats valides', function (string $numero, string $attendu) {
            $numeroEmplacement = NumeroEmplacement::fromString($numero);

            expect($numeroEmplacement->valeur())->toBe($attendu);
        })->with([
            ['A1', 'A01'],
            ['A12', 'A12'],
            ['B5', 'B05'],
            ['VIP1', 'VIP1'],
            ['VIP123', 'VIP123'],
            ['Z99', 'Z99'],
            ['PMR1', 'PMR1'],
            ['AA', 'AA'],
            ['Z999', 'Z999'],
        ]);

        it('rejette les formats invalides', function (string $numero) {
            expect(fn () => NumeroEmplacement::fromString($numero))
                ->toThrow(InvalidArgumentException::class);
        })->with([
            '',           // vide
            ' ',          // espaces seulement
            'A',          // trop court
            'A1234567890', // trop long
            'a-12',       // caractère spécial
            'A 12',       // espace interne (après normalisation)
            'A12@',       // caractère spécial
            'A12#',       // caractère spécial
            'A12.',       // point
            'A12-',       // tiret
        ]);

        it('normalise correctement les entrées diverses', function () {
            $numero1 = NumeroEmplacement::fromString('  a5  ');
            $numero2 = NumeroEmplacement::fromString('A5');
            $numero3 = NumeroEmplacement::fromString('A05');

            expect($numero1->valeur())->toBe('A05');
            expect($numero2->valeur())->toBe('A05');
            expect($numero3->valeur())->toBe('A05');

            expect($numero1->equals($numero2))->toBeTrue();
            expect($numero2->equals($numero3))->toBeTrue();
        });

        it('gère les numéros spéciaux', function () {
            $vip = NumeroEmplacement::fromString('VIP1');
            $pmr = NumeroEmplacement::fromString('PMR1');
            $bar = NumeroEmplacement::fromString('BAR12');

            expect($vip->valeur())->toBe('VIP1');
            expect($pmr->valeur())->toBe('PMR1');
            expect($bar->valeur())->toBe('BAR12');
        });
    });

    describe('Immutabilité et Structure', function () {
        it('est immutable par design', function () {
            $classReflection = new ReflectionClass(NumeroEmplacement::class);
            expect($classReflection->isReadOnly())->toBeTrue();
            expect($classReflection->isFinal())->toBeTrue();

            $properties = $classReflection->getProperties();
            foreach ($properties as $property) {
                expect($property->isReadOnly())->toBeTrue();
            }
        });

        it('maintient l\'immutabilité', function () {
            $numero1 = NumeroEmplacement::fromString('A12');
            $numero2 = NumeroEmplacement::fromString('B12');

            expect($numero1)->not->toBe($numero2);
            expect($numero1->valeur())->toBe('A12');
            expect($numero2->valeur())->toBe('B12');
        });
    });
});
