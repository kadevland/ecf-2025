<?php

declare(strict_types=1);

use App\Domain\Enums\Pays;
use App\Domain\ValueObjects\Commun\Telephone;

describe('Telephone Value Object', function () {

    describe('Construction et Factory Methods', function () {
        it('crée un téléphone français valide', function () {
            $tel = Telephone::francais('06.12.34.56.78');

            expect($tel->numero)->toBe('0612345678'); // Normalisé
            expect($tel->pays)->toBe(Pays::France);
        });

        it('crée un téléphone belge valide', function () {
            $tel = Telephone::belge('0485-12-34-56');

            expect($tel->numero)->toBe('0485123456'); // Normalisé
            expect($tel->pays)->toBe(Pays::Belgique);
        });

        it('crée depuis format international', function () {
            $telFr = Telephone::fromInternational('+33 6 12 34 56 78');
            $telBe = Telephone::fromInternational('+32 485 12 34 56');

            expect($telFr->pays)->toBe(Pays::France);
            expect($telBe->pays)->toBe(Pays::Belgique);
        });
    });

    describe('Getters et Accesseurs', function () {
        it('expose le numéro normalisé', function () {
            $tel = Telephone::francais('06.12.34.56.78');

            expect($tel->numero)->toBe('0612345678');
        });

        it('expose le pays', function () {
            $telFr = Telephone::francais('06.12.34.56.78');
            $telBe = Telephone::belge('0485-12-34-56');

            expect($telFr->pays)->toBe(Pays::France);
            expect($telBe->pays)->toBe(Pays::Belgique);
        });
    });

    describe('Prédicats et États', function () {
        it('détecte les mobiles français', function () {
            $mobile06 = Telephone::francais('06.12.34.56.78');
            $mobile07 = Telephone::francais('07.12.34.56.78');
            $fixe01   = Telephone::francais('01.12.34.56.78');

            expect($mobile06->estMobile())->toBeTrue();
            expect($mobile07->estMobile())->toBeTrue();
            expect($fixe01->estMobile())->toBeFalse();
        });

        it('détecte les fixes français', function () {
            $fixe01   = Telephone::francais('01.12.34.56.78');
            $fixe02   = Telephone::francais('02.12.34.56.78');
            $mobile06 = Telephone::francais('06.12.34.56.78');

            expect($fixe01->estFixe())->toBeTrue();
            expect($fixe02->estFixe())->toBeTrue();
            expect($mobile06->estFixe())->toBeFalse();
        });

        it('détecte les mobiles belges', function () {
            $mobile = Telephone::belge('0485-12-34-56');
            $fixe   = Telephone::belge('02-123-45-67');

            expect($mobile->estMobile())->toBeTrue();
            expect($mobile->estFixe())->toBeFalse();
            expect($fixe->estMobile())->toBeFalse();
            expect($fixe->estFixe())->toBeTrue();
        });
    });

    describe('Comparaisons', function () {
        it('compare correctement deux téléphones identiques', function () {
            $tel1 = Telephone::francais('06.12.34.56.78');
            $tel2 = Telephone::francais('06-12-34-56-78'); // Format différent mais même numéro

            expect($tel1->equals($tel2))->toBeTrue();
        });

        it('différencie les numéros différents', function () {
            $tel1 = Telephone::francais('06.12.34.56.78');
            $tel2 = Telephone::francais('07.12.34.56.78');

            expect($tel1->equals($tel2))->toBeFalse();
        });

        it('différencie les pays', function () {
            $telFr = Telephone::francais('06.12.34.56.78');
            $telBe = Telephone::belge('0485-12-34-56');

            expect($telFr->equals($telBe))->toBeFalse();
        });
    });

    describe('Validation et Règles Métier', function () {
        it('rejette les numéros français invalides', function (string $numeroInvalide) {
            expect(fn () => Telephone::francais($numeroInvalide))
                ->toThrow(InvalidArgumentException::class);
        })->with([
            '05.12.34.56',     // 9 chiffres
            '06.12.34.56.78.90', // 12 chiffres
            '16.12.34.56.78',  // Préfixe invalide
            '',                // Vide
            'abc.def.ghi',     // Lettres
        ]);

        it('rejette les numéros belges invalides', function (string $numeroInvalide) {
            expect(fn () => Telephone::belge($numeroInvalide))
                ->toThrow(InvalidArgumentException::class);
        })->with([
            '0485-12-34',      // Trop court
            '0485-12-34-56-78', // Trop long
            '0123-45-67-89',   // Préfixe invalide
            '',                // Vide
        ]);

        it('rejette les formats internationaux non supportés', function () {
            expect(fn () => Telephone::fromInternational('+49 123 456 789'))
                ->toThrow(InvalidArgumentException::class, 'Format international non supporté');
        });
    });

    describe('Immutabilité et Structure', function () {
        it('est immutable par design', function () {
            $classReflection = new ReflectionClass(Telephone::class);
            expect($classReflection->isReadOnly())->toBeTrue();
            expect($classReflection->isFinal())->toBeTrue();

            $properties = $classReflection->getProperties();
            foreach ($properties as $property) {
                expect($property->isReadOnly())->toBeTrue();
            }
        });
    });
});
