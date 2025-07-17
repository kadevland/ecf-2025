<?php

declare(strict_types=1);

use App\Domain\Enums\Pays;

describe('Pays Enum', function () {

    it('a les cases France et Belgique', function () {
        expect(Pays::cases())->toHaveCount(2);
        expect(Pays::France->value)->toBe('FR');
        expect(Pays::Belgique->value)->toBe('BE');
    });

    it('peut être créé depuis une valeur', function () {
        expect(Pays::from('FR'))->toBe(Pays::France);
        expect(Pays::from('BE'))->toBe(Pays::Belgique);
    });

    it('lève une exception pour valeur invalide', function () {
        expect(fn () => Pays::from('DE'))
            ->toThrow(ValueError::class);
    });

    it('retourne le bon label français', function () {
        expect(Pays::France->label())->toBe('France');
        expect(Pays::Belgique->label())->toBe('Belgique');
    });

    it('retourne le bon indicatif téléphone', function () {
        expect(Pays::France->indicatifTelephone())->toBe('+33');
        expect(Pays::Belgique->indicatifTelephone())->toBe('+32');
    });

    it('retourne la bonne longueur téléphone', function () {
        expect(Pays::France->longueurTelephone())->toBe([10]);
        expect(Pays::Belgique->longueurTelephone())->toBe([9, 10]);
    });

    it('retourne le bon préfixe national', function () {
        expect(Pays::France->prefixeNational())->toBe('0');
        expect(Pays::Belgique->prefixeNational())->toBe('0');
    });

    it('retourne les bons préfixes mobiles', function () {
        expect(Pays::France->prefixesMobiles())->toBe(['06', '07']);
        expect(Pays::Belgique->prefixesMobiles())->toBe(['04']);
    });

    it('retourne le bon format code postal', function () {
        expect(Pays::France->formatCodePostal())->toBe('\d{5}');
        expect(Pays::Belgique->formatCodePostal())->toBe('\d{4}');
    });

    it('retourne la bonne plage code postal', function () {
        expect(Pays::France->plageCodePostal())->toBe([1000, 99999]);
        expect(Pays::Belgique->plageCodePostal())->toBe([1000, 9999]);
    });

});
